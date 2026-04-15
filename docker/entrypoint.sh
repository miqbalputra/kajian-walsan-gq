#!/bin/sh
set -e

# Copy .env jika belum ada
if [ ! -f /var/www/html/.env ]; then
    cp /var/www/html/.env.example /var/www/html/.env
fi

# Masuk ke direktori app
cd /var/www/html

# Generate key jika belum ada
php artisan key:generate --no-interaction --force

# Jalankan migrations
php artisan migrate --force --no-interaction

# Publish Pulse config & dashboard (jika belum ada)
php artisan vendor:publish --provider="Laravel\Pulse\PulseServiceProvider" --no-interaction 2>/dev/null || true

# Hapus compiled views agar Blaze bisa compile ulang dengan optimasi
php artisan view:clear

# Cache config untuk production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Pastikan direktori upload ada
mkdir -p /var/www/html/storage/app/public/attendance-proofs
mkdir -p /var/www/html/storage/app/public/izin-documents
mkdir -p /var/www/html/storage/app/public/reupload-proofs
mkdir -p /var/www/html/storage/app/livewire-tmp

# Pastikan /tmp writable
chmod 1777 /tmp 2>/dev/null || true

# Pastikan storage link ada
php artisan storage:link --force 2>/dev/null || true

# Set permissions
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Buat log dir untuk supervisor
mkdir -p /var/log/supervisor

# Jalankan supervisord
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf