# Cinema Reservation System

[![Build & Deploy on Release](https://github.com/KouniakiosKatakthths/Uniwa-Ecommerce/actions/workflows/docker-publish.yml/badge.svg)](https://github.com/KouniakiosKatakthths/Uniwa-Ecommerce/actions/workflows/docker-publish.yml)
[![Latest Release](https://img.shields.io/github/v/tag/KouniakiosKatakthths/Uniwa-Ecommerce?label=Latest+Release)](https://github.com/KouniakiosKatakthths/Uniwa-Ecommerce/releases/latest)
[![License: GPL v3](https://img.shields.io/badge/License-GPLv3-blue.svg)](https://www.gnu.org/licenses/gpl-3.0)

A web-based cinema reservation system built with **Laravel**, **Tailwind CSS** and **Alpine JS**, allowing users to browse movies, pick their seats, and receive a QR-coded ticket from their browser.

This project was developed for the course of E-Commerse in University of West Attica. 

## Features
- **Movie Browsing & Showtimes**: Explore currently showing and upcoming films with schedules by date and cinema hall.
- **Interactive Seat Selection**: Visual seat map per screening, with real-time availability to avoid double bookings.
- **User Authentication**: Secure registration and login system with personal reservation history.
- **QR Ticket Generation**: Each confirmed booking generates a unique QR code ticket, ready to present at the door.
- **Admin Panel**: Manage movies, screenings, halls, and reservations from a dedicated back-office interface.
- [**TMDB library**](https://www.themoviedb.org/): For easier managment of movie data an interface with the TMDB api has been build. The user can choose to load data for a movie based on the TMDB ID. Any movie with an assosiated TMDB ID will also get automatic critics updates from the TMDB system.

## Demo

For the demo website you can use the following credentials
- **Admin** \
email: admin@admin.com \
password: password3
- **Clerk** \
email: clerk@clerk.com \
password: password2
- **User** \
email: user@user.com \
password: password1 

## Getting started
__*The following instructions is for local development, for docker deployment look the [docker README](docker/README.md) file.*__

*The instructions below is an overview of the required steps. For convenience the [`setup.sh`](setup.sh) script has been created for linux or WSL systems. Execute like so: `./setup.sh`. If needed add execution rights: `chmod +x ./setup.sh`*

Before you start it is recommented that you take a look at the [`.env.examble`](.env.examble) file since it contains info about the configuration of the application. 
Make sure you have installed [Git](https://git-scm.com/) on your machine. For windowds users the use of [WSL](https://learn.microsoft.com/en-us/windows/wsl/install) is recommended.

### Installation
1. **Clone the repository**
```bash
git clone https://github.com/your-username/cinema-reservation.git
cd cinema-reservation
```
2. **Create the env file based on the examble**

```bash
cp .env.example .env
```
After creating the `.env` file edit it and fill the nessasery information for your configuration.

3. **Download dependencies**

You will need php8.2 and mysql server for this project.

In debian based systems install them like so:
```bash
# Install mysql server
sudo apt install -y mysql-server
sudo systemctl start mysql
sudo systemctl enable mysql

# Install php dependencies
sudo apt install -y php php-cli php-mbstring php-xml php-curl \
    php-zip php-bcmath php-tokenizer php8.2-pdo php-mysql php-sqlite3
php -v
```
You will also need php composer. To install this follow the installation in the [official composer website](https://getcomposer.org/download/).

4. **Install NodeJS**

NodeJS is also required for tailwind css to function. It is recommented that you install node js using the [Node Version Manager (NVM)](https://github.com/nvm-sh/nvm).

With NVM you can install node like so:
```bash
nvm install node
node -v
npm -v
```
5. **Install project packages**

Using composer now you can install the project php packages and using npm you can install the js packages, tailwind css in this case.
```bash
composer install --no-interaction --prefer-dist --optimize-autoloader
npm install
```
6. **Create MySQL database**

Connect to your mysql instance and create database and user like so:
Replace (...) with your actual data.
```SQL
CREATE DATABASE IF NOT EXISTS (db_name);
CREATE USER IF NOT EXISTS '(db_user)'@'localhost' IDENTIFIED BY '(db_pass)';
GRANT ALL PRIVILEGES ON \`(db_name)\`.* TO '(db_user)'@'localhost';
FLUSH PRIVILEGES;
```
Dont forget to update your .env file with the new values. After that you can install the project's migrations:
```bash
php artisan migrate:fresh

#Optionally run the default seaders of the project
php artisan db:seed --force
```
You can also run `php artisan migrate:fresh --seed` to run the build in seeders as well.

7. **Finalize setup**

Install a laravel app key, link the storage and run the project!
```bash
php artisan key:generate

#Link the storage so uploads in /storage are reachable by php
php artisan storage:link
```

You can run the project with:
```bash
npm run dev
```
Npm is used because of the js dependencies.

## Local deploy

*For convenience the [`deploy.sh`](deploy.sh) script has been created for linux or WSL systems.*

To deploy the project to a local apache server you can follow the steps:

1. **Install apache**
```bash
sudo apt install -y apache2
```

2. **Build js project**
```bash
npm run build
```

3. Copy project files and create storage
```bash
sudo mkdir -p /var/www/cinema
sudo rsync -a \
  --exclude='.git' \
  --exclude='node_modules' \
  --exclude='public/storage' \
  ./ /var/www/cinema

# Create storage 
sudo chown -R www-data:www-data /var/www/cinema/storage
sudo chown -R www-data:www-data /var/www/cinema/bootstrap/cache
sudo chmod -R 775 /var/www/cinema/storage
sudo chmod -R 775 /var/www/cinema/bootstrap/cache
sudo chmod -R 755 /var/www/cinema/public

#Finally link storage for php to discover
sudo php "/var/www/cinema/artisan" storage:link
```

4. **Enable rewrites**
```
sudo a2enmod rewrite
```

5. **Configure apache virtual host**

Save on `/etc/apache2/sites-available/{server_name}.conf`
Where `server_name` your server's name.
```conf
<VirtualHost *:80>
    ServerName {server_name}
    DocumentRoot /var/www/cinema/public
 
    <Directory /var/www/cinema/public>
        Options FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
 
    ErrorLog \${APACHE_LOG_DIR}/{server_name}_error.log
    CustomLog \${APACHE_LOG_DIR}/{server_name}_access.log combined
</VirtualHost>
```

Activate configuration and restart apache for effects to take place.
```bash
sudo a2ensite "{server_name}.conf"
sudo systemctl restart apache2
```

6. **Optimise laravel**
```bash
sudo php "/var/www/cinema/artisan" config:cache
sudo php "/var/www/cinema/artisan" route:cache
sudo php "/var/www/cinema/artisan" view:cache
```

7. **Finalize**

IMPORTANT: Don't forget to update your `.env` file in `/var/www/cinema/` to be switched into production configuration. Also change the app URL to the actual url your application uses.

*If using a local domain, add this to /etc/hosts:  127.0.0.1  {server_name}*

## License
 
This is an open-source project licensed under the [GPL 3.0 license](LICENSE).
