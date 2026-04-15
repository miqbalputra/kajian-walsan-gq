#!/bin/sh
set -e

# Copy .env jika belum ada
if [ ! -f /var/www/html/.env ]; then
    cp /var/www/html/.env.example /var/www/html/.env
fi

# Masuk ke direktori app
cd /var/www/html

# Fix permissions early
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Generate key if not set (Safeguard)
if [ -z "$APP_KEY" ]; then
    php artisan key:generate --no-interaction --force
fi

# Publish & Migrate Pulse
php artisan vendor:publish --tag=pulse-config --tag=pulse-dashboard --tag=pulse-migrations --force -n
php artisan migrate --force --no-interaction

# Cache for performance
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Pastikan storage link ada
php artisan storage:link --force 2>/dev/null || true

# Start Supervisor
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf