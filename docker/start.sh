#!/usr/bin/env sh
set -e

cd /var/www/html

php artisan storage:link || true
php artisan migrate --force --no-interaction
php artisan config:cache
php artisan route:cache || true
php artisan view:cache || true

exec apache2-foreground
