#!/bin/bash
set -e

echo "Waiting for MySQL..."
until php -r "new PDO('mysql:host=${DB_HOST};port=${DB_PORT:-3306};dbname=${DB_DATABASE}', '${DB_USERNAME}', '${DB_PASSWORD}');" 2>/dev/null; do
    echo "  Not ready, retrying in 3s..."
    sleep 3
done
echo "MySQL ready!"

if [ "${DEMO_MODE}" = "true" ]; then
    echo "@@@@@@@@@@@@@@@@@ WARNING @@@@@@@@@@@@@@@@@"
    echo "@ Demo mode is enabled, all the data will @"
    echo "@ be lost after 24 hours. DISABLE IF NOT  @"
    echo "@ INTENTIONAL. (DEMO_MODE=false in .env)  @"
    echo "@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@"

    echo "Installing dev dependencies for seeders..."
    composer install --no-interaction --optimize-autoloader

    php artisan config:clear
    php artisan migrate:fresh --force --no-interaction
    
    sleep 3

    echo "Setting up admin user..."
    php artisan admin:create

    echo "Running seeding with test data..."
    php artisan db:seed --force --no-interaction
else
    echo "Running migrations..."
    php artisan migrate --force --no-interaction

    echo "Setting up admin user..."
    php artisan admin:create
fi

echo "Creating storage symlink..."
php artisan storage:link --force 2>/dev/null || true

echo "Running Laravel optimizations..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

echo "Cinema app ready! Starting Apache..."
echo "~ 67 on a merry rizzmass 67 on a merry rizzmass ~"
echo "¯\_(ツ)_/¯"
exec "$@"