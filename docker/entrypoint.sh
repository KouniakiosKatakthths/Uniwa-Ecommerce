#!/bin/bash
set -e

echo "Waiting for MySQL..."
until php -r "new PDO('mysql:host=${DB_HOST};port=${DB_PORT:-3306};dbname=${DB_DATABASE}', '${DB_USERNAME}', '${DB_PASSWORD}');" 2>/dev/null; do
    echo "  Not ready, retrying in 3s..."
    sleep 3
done
echo "MySQL ready!"

echo "Generating app key if not exist..."
php artisan key:generate --no-interaction --force

echo "Running migrations..."
php artisan migrate --force --no-interaction

echo "Creating storage symlink..."
php artisan storage:link --force 2>/dev/null || true

echo "Running Laravel optimizations..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

echo "Cinema app ready! Starting Apache..."
exec "$@"