#!/usr/bin/env bash
set -e

mkdir -p storage/framework/sessions storage/framework/views storage/framework/cache storage/logs bootstrap/cache
chmod -R 775 storage bootstrap/cache

php artisan config:clear || true
php artisan cache:clear || true
php artisan view:clear || true

php-fpm -F
