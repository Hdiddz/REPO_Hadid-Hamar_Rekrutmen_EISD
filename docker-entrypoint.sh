#!/bin/bash
set -e

# Render assigns dynamic port via $PORT
if [ -n "$PORT" ]; then
    sed -i "s/80/$PORT/g" /etc/apache2/ports.conf /etc/apache2/sites-available/*.conf
fi

# Ensure runtime directories and sqlite database exist with proper permissions
mkdir -p storage/framework/sessions storage/framework/views storage/framework/cache storage/logs database
touch database/database.sqlite
chown -R www-data:www-data storage bootstrap/cache database
chmod -R 775 storage bootstrap/cache database

# Laravel runtime bootstrap
php artisan storage:link || true
php artisan config:clear
php artisan migrate --force
php artisan db:seed --force

exec apache2-foreground
