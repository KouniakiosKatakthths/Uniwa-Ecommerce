#!/bin/bash

source ./setup.sh

read -p "$(echo -e ${YELLOW}Enter server name / domain [default: cinema.local]:${NC} )" SERVER_NAME
read -p "$(echo -e ${YELLOW}Enter deploy path [default: /var/www/cinema]:${NC} )" DEPLOY_PATH

SERVER_NAME=${SERVER_NAME:-cinema.local}
DEPLOY_PATH=${DEPLOY_PATH:-/var/www/cinema}
VHOST_FILE="/etc/apache2/sites-available/${SERVER_NAME}.conf"

# ── 1. Build frontend assets ──
info "Building frontend assets (npm run build)..."
npm run build

# ── 2. Copy project files to deploy path ──
info "Copying project to ${DEPLOY_PATH}..."
sudo mkdir -p "$DEPLOY_PATH"
sudo rsync -a \
  --exclude='.git' \
  --exclude='node_modules' \
  --exclude='public/storage' \
  ./ "$DEPLOY_PATH/"

# ── 3. Set permissions ──
info "Setting permissions..."
sudo chown -R www-data:www-data "$DEPLOY_PATH"/storage
sudo chown -R www-data:www-data "$DEPLOY_PATH"/bootstrap/cache
sudo chmod -R 775 "$DEPLOY_PATH"/storage
sudo chmod -R 775 "$DEPLOY_PATH"/bootstrap/cache
sudo chmod -R 755 "$DEPLOY_PATH"/public

# ── 4. Storage link ──
sudo rm -rf "$DEPLOY_PATH/public/storage"
sudo mkdir -p "$DEPLOY_PATH/storage/app/public"

info "Creating storage symlink..."
sudo php "$DEPLOY_PATH/artisan" storage:link

# ── 5. Install Apache if missing ──
if ! command -v apache2 &>/dev/null; then
    info "Installing Apache..."
    sudo apt install -y apache2
else
    warning "Apache already installed."
fi

# ── 6. Enable mod_rewrite ──
info "Enabling mod_rewrite..."
sudo a2enmod rewrite

# ── 7. Write Virtual Host config ──
info "Creating Apache virtual host: ${VHOST_FILE}..."
sudo tee "$VHOST_FILE" > /dev/null <<EOF
<VirtualHost *:80>
    ServerName ${SERVER_NAME}
    DocumentRoot ${DEPLOY_PATH}/public
 
    <Directory ${DEPLOY_PATH}/public>
        Options FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
 
    ErrorLog \${APACHE_LOG_DIR}/${SERVER_NAME}_error.log
    CustomLog \${APACHE_LOG_DIR}/${SERVER_NAME}_access.log combined
</VirtualHost>
EOF

# ── 8. Enable site & restart Apache ──
info "Enabling site and restarting Apache..."
sudo a2ensite "${SERVER_NAME}.conf"
sudo systemctl restart apache2

# ── 9. Set .env to production ──
info "Updating .env for production..."
sudo sed -i "s/APP_ENV=.*/APP_ENV=production/" "$DEPLOY_PATH/.env"
sudo sed -i "s/APP_DEBUG=.*/APP_DEBUG=false/" "$DEPLOY_PATH/.env"
sudo sed -i "s|APP_URL=.*|APP_URL=http://${SERVER_NAME}|" "$DEPLOY_PATH/.env"

warning "Using same URL for ASSET_URL: http://${SERVER_NAME}"
sudo sed -i "s|ASSET_URL=.*|ASSET_URL=http://${SERVER_NAME}|" "$DEPLOY_PATH/.env"

# ── 10. Laravel production optimizations ──
info "Running Laravel production optimizations..."
sudo php "$DEPLOY_PATH/artisan" config:cache
sudo php "$DEPLOY_PATH/artisan" route:cache
sudo php "$DEPLOY_PATH/artisan" view:cache

echo ""
info "Deploy complete! App is live at: http://${SERVER_NAME}"
warning "If using a local domain, add this to /etc/hosts:  127.0.0.1  ${SERVER_NAME}"
