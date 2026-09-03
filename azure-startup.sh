#!/bin/bash

echo "Configuring Nginx for Laravel DocumentRoot..."
if [ -d "/etc/nginx/sites-available" ]; then
    cp /home/site/wwwroot/nginx.conf /etc/nginx/sites-available/default
fi
if [ -d "/etc/nginx/sites-enabled" ]; then
    cp /home/site/wwwroot/nginx.conf /etc/nginx/sites-enabled/default
fi
if [ -d "/etc/nginx/conf.d" ]; then
    cp /home/site/wwwroot/nginx.conf /etc/nginx/conf.d/default.conf
fi
service nginx reload || /usr/sbin/nginx -s reload || true

cd /home/site/wwwroot || exit 1

# Ensure storage directories and permissions
mkdir -p storage/framework/sessions storage/framework/views storage/framework/cache storage/logs database
touch database/database.sqlite
chmod -R 777 storage bootstrap/cache database || true

# Remove runner-specific cached files
rm -f bootstrap/cache/*.php

# Laravel initialization
php artisan storage:link || true
php artisan config:clear || true
php artisan migrate --force || true
php artisan db:seed --force || true

echo "Azure startup complete!"
