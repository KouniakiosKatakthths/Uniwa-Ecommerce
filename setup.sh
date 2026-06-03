#!/bin/bash
set -e  # Exit immediately on error
 
# ─────────────────────────────────────────
#  Colors for output
# ─────────────────────────────────────────
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

# Log helpers
info()    { echo -e "${GREEN}[INFO]${NC} $1"; }
warning() { echo -e "${YELLOW}[WARN]${NC} $1"; }
error()   { echo -e "${RED}[ERROR]${NC} $1"; exit 1; }

# Check OS
if [[ "$OSTYPE" != "linux-gnu"* ]]; then
    error "This script only supports Linux (Debian/Ubuntu). For Mac, use Homebrew: brew install php"
fi

# ========= INSTALL PHP ============
info "Updating package list..."
sudo apt update -qq
 
info "Installing PHP and required extensions..."
sudo apt install -y php php-cli php-mbstring php-xml php-curl \
    php-zip php-bcmath php-tokenizer php-pdo php-mysql php-sqlite3
 
info "PHP version: $(php -v | head -n 1)"

# ========= INSTALL MYSQL ============
if ! command -v mysql &>/dev/null; then
    info "Installing MySQL..."
    sudo apt install -y mysql-server
    sudo systemctl start mysql
    sudo systemctl enable mysql
else
    warning "MySQL already installed: $(mysql --version)"
fi

# ========= INSTALL COMPOSER ============
if command -v composer &>/dev/null; then
    warning "Composer already installed: $(composer --version)"
else
    info "Installing Composer..."
 
    EXPECTED_CHECKSUM="$(php -r 'copy("https://composer.github.io/installer.sig", "php://stdout");')"
    php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
    ACTUAL_CHECKSUM="$(php -r "echo hash_file('sha384', 'composer-setup.php');")"
 
    if [ "$EXPECTED_CHECKSUM" != "$ACTUAL_CHECKSUM" ]; then
        rm composer-setup.php
        error "Composer installer checksum mismatch! Aborting for security."
    fi
 
    php composer-setup.php --quiet
    rm composer-setup.php
    sudo mv composer.phar /usr/local/bin/composer
    sudo chmod +x /usr/local/bin/composer
 
    info "Composer version: $(composer --version)"
fi

# ========= INSTALL NODEJS AND NPM ============
if command -v node &>/dev/null; then
    warning "Node.js already installed: $(node -v)"
else
    if command -v nvm &>/dev/null; then
        warning "Nvm already installed $(nvm -v)"
    else
        info "Installing nvm..."
        curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.40.4/install.sh | bash

        # Reload shell environment for current script
        export NVM_DIR="$HOME/.nvm"
        [ -s "$NVM_DIR/nvm.sh" ] && . "$NVM_DIR/nvm.sh"
    fi

    info "Installing Node.js & npm using nvm..."
    nvm install node
    info "Node.js installed: $(node -v), npm: $(npm -v)"
fi
 
# ========= INSTALL PROJECT DEPENDENCIES ============ 
if [ -f "composer.json" ]; then
    info "Found composer.json — installing project dependencies..."
    composer install --no-interaction --prefer-dist --optimize-autoloader
else
    warning "No composer.json found. Skipping dependency install."
fi

# ========= SETUP ENV FILE ============ 
if [ -f ".env.example" ] && [ ! -f ".env" ]; then
    info "Copying .env.example to .env..."
    cp .env.example .env
elif [ -f ".env" ]; then
    warning ".env already exists, skipping."
fi

# ============ CREATE MYSQL DATABASE ============
read -p "$(echo -e ${YELLOW}Run database setup? [y/N]:${NC} )" RUN_DB_SETUP
if [[ "$RUN_DB_SETUP" =~ ^[Yy]$ ]]; then
    read -p "$(echo -e ${YELLOW}Enter MySQL root password [leave empty if none]:${NC} )" MYSQL_ROOT_PASS
    read -p "$(echo -e ${YELLOW}Enter database name [default: uniwa_ecommerce]:${NC} )" DB_NAME
    read -p "$(echo -e ${YELLOW}Enter new DB username [default: uniwa_user]:${NC} )" DB_USER
    read -p "$(echo -e ${YELLOW}Enter new DB user password:${NC} )" DB_PASS
    
    DB_NAME=${DB_NAME:-uniwa_ecommerce}
    DB_USER=${DB_USER:-uniwa_user}
    
    if [ -z "$DB_PASS" ]; then
        error "DB user password cannot be empty."
    fi
    
    if [ -z "$MYSQL_ROOT_PASS" ]; then
        MYSQL_CMD="sudo mysql"
    else
        MYSQL_CMD="mysql -u root -p${MYSQL_ROOT_PASS}"
    fi
 
    info "Creating database '$DB_NAME'..."
    $MYSQL_CMD <<EOF
CREATE DATABASE IF NOT EXISTS \`${DB_NAME}\`;
CREATE USER IF NOT EXISTS '${DB_USER}'@'localhost' IDENTIFIED BY '${DB_PASS}';
GRANT ALL PRIVILEGES ON \`${DB_NAME}\`.* TO '${DB_USER}'@'localhost';
FLUSH PRIVILEGES;
EOF
 
    info "Database '$DB_NAME' and user '$DB_USER' created with restricted access."
 
    # Update .env with new user credentials (not root!)
    sed -i "s/DB_DATABASE=.*/DB_DATABASE=${DB_NAME}/" .env
    sed -i "s/DB_USERNAME=.*/DB_USERNAME=${DB_USER}/" .env
    sed -i "s/DB_PASSWORD=.*/DB_PASSWORD=${DB_PASS}/" .env
    info "Updated .env with dedicated user credentials."
fi

# ========= INSTALL JS DEPENCENCIES ============ 
if [ -f "package.json" ]; then
    info "Installing JS dependencies..."
    npm install
fi


# ========= GENERATE APP KEY ============ 
info "Generating Laravel app key..."
php artisan key:generate

# ========= RUN MIGRATIONS ============ 
if [ -f "artisan" ]; then
    read -p "$(echo -e ${YELLOW}Run database migrations? [y/N]:${NC} )" RUN_MIGRATE
    if [[ "$RUN_MIGRATE" =~ ^[Yy]$ ]]; then
        info "Running migrations..."
        php artisan migrate --force

        # Run seeders
        read -p "$(echo -e ${YELLOW}Seed database with sample data? [y/N]:${NC} )" RUN_SEED
        if [[ "$RUN_SEED" =~ ^[Yy]$ ]]; then
            info "Seeding database..."
            php artisan db:seed --force
        fi
    fi
fi

echo ""
echo -e "${GREEN} Setup complete!${NC}"
echo -e "  Start the dev server with: ${YELLOW}php artisan serve${NC}"
