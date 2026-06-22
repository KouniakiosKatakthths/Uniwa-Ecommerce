# System Docker Setup

This document will guide you for the docker deployment of the Cinema Reservation System.

For docker deployment it is recommented that you use a proxy container like nginx proxy manager, cuddy, traefik, etc.

The first step is to copy the contents of `.env.examble` file into an actual `.env` file, thats local to your machine.

## Apache host

For the apache service to work you need to provide the config file. This file must be mounted on the docker's `/etc/apache2/sites-available/000-default.conf:ro` file.

Sample apache config file with nginx config.
```
<VirtualHost *:80>
    ServerAdmin webmaster@localhost
    DocumentRoot /var/www/html/public

    <Directory /var/www/html/public>
        Options FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    # Trust Nginx Proxy Manager forwarded IPs
    RemoteIPHeader X-Forwarded-For
    RemoteIPTrustedProxy 172.16.0.0/12

    ErrorLog ${APACHE_LOG_DIR}/cinema_error.log
    CustomLog ${APACHE_LOG_DIR}/cinema_access.log combined
</VirtualHost>
```

## Scheduler

In order for Laravel to run scheduled tasks, a `scheduler` container is used. It runs the scheduler every 60 seconds to check for tasks to run.

## Important ENV variables
 
### Database host
When running in Docker, the database host must point to the service name, **not** `127.0.0.1`

### Demo Mode

This mode is meant only for demo of the application and should **NEVER** be used in real enviroments. The demo mode resets the database every 24h and forbits admins from changing ther passwords.
```dotenv
# WARNING: When enabled, ALL data is wiped and reseeded on each restart.
# Admin passwords cannot be changed in demo mode.
DEMO_MODE=false
```

### Admin account 

The first account that will be created by default from the application will be an admin account. You can spesify the credenticals of that account with the following variables

```dotenv
ADMIN_EMAIL=
ADMIN_NAME=
ADMIN_PASSWORD=
```

_The default application values are \
ADMIN_EMAIL: admin@admin.com \
ADMIN_NAME: admin \
ADMIN_PASSWORD: password_

### Mail Configuration
 
For production, replace the Mailpit defaults with your SMTP provider:
 
```dotenv
MAIL_MAILER=smtp
MAIL_HOST=smtp.yourprovider.com
MAIL_PORT=587
MAIL_USERNAME=your@email.com
MAIL_PASSWORD=yourpassword
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@cinema.com"
```

## Sample `docker-compose.yaml`
```yaml
services:
  app:
    image: ghcr.io/kouniakioskatakthths/uniwa-ecommerce:latest
    container_name: cinelux
    restart: unless-stopped
    env_file: .env
    volumes:
      - ./config/apache.conf:/etc/apache2/sites-available/000-default.conf:ro
      - cinelux_storage:/var/www/html/storage
      - cinelux_cache:/var/www/html/bootstrap/cache
    depends_on:
      db:
        condition: service_healthy
    networks:
      - cinelux_network

  scheduler:
    image: ghcr.io/kouniakioskatakthths/uniwa-ecommerce:latest
    container_name: cinema_scheduler
    restart: unless-stopped
    env_file: .env
    volumes:
      - cinelux_storage:/var/www/html/storage
      - cinelux_cache:/var/www/html/bootstrap/cache
    depends_on:
      db:
        condition: service_healthy
    networks:
      - cinelux_network
    entrypoint: []
    command: >
      sh -c "while true; do php /var/www/html/artisan schedule:run --no-interaction; sleep 60; done"

  db:
    image: mariadb:10.6
    container_name: cinema_db
    restart: unless-stopped
    environment:
      MARIADB_DATABASE: ${DB_DATABASE}
      MARIADB_USER: ${DB_USERNAME}
      MARIADB_PASSWORD: ${DB_PASSWORD}
      MARIADB_ROOT_PASSWORD: ${DB_ROOT_PASSWORD}
    volumes:
      - cinelux_db:/var/lib/mysql
    healthcheck:
      test: ["CMD", "mysqladmin", "ping", "-h", "localhost", "-u${DB_USERNAME}", "-p${DB_PASSWORD}"]
      interval: 10s
      timeout: 5s
      retries: 5
    networks:
      - cinelux_network

volumes:
  cinelux_db:
  cinelux_storage:
  cinelux_cache:

networks:
  cinelux_network:
```