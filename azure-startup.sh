#!/bin/bash
set -e

echo "Configuring Nginx for Laravel DocumentRoot..."
cp /home/site/wwwroot/nginx.conf /etc/nginx/sites-available/default
service nginx reload

cd /home/site/wwwroot

# Ensure storage directories and permissions
mkdir -p storage/framework/{sessions,views,cache} storage/logs database
touch database/database.sqlite
chmod -R 775 storage bootstrap/cache database

# Laravel initialization
php artisan storage:link || true
php artisan config:clear
php artisan migrate --force
php artisan db:seed --force

echo "Azure startup complete!"
