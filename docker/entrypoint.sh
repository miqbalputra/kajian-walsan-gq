#!/bin/sh
set -e

# Copy .env jika belum ada
if [ ! -f /var/www/html/.env ]; then
    cp /var/www/html/.env.example /var/www/html/.env
fi

# Masuk ke direktori app
cd /var/www/html

# Paksa bersihkan cache lama dari image/container sebelumnya.
# Ini penting di deploy VPS agar Blade terbaru dari GitHub tidak kalah oleh compiled view lama.
rm -f /var/www/html/bootstrap/cache/*.php 2>/dev/null || true
rm -f /var/www/html/storage/framework/views/*.php 2>/dev/null || true
php -r "if (function_exists('opcache_reset')) { opcache_reset(); }" 2>/dev/null || true
php artisan optimize:clear --no-interaction || true

# Generate key jika belum ada
php artisan key:generate --no-interaction --force

# Jalankan migrations
php artisan migrate --force --no-interaction

# Publish Pulse config & dashboard (jika belum ada)
php artisan vendor:publish --provider="Laravel\Pulse\PulseServiceProvider" --no-interaction 2>/dev/null || true

# Hapus compiled views agar Blade/Blaze compile ulang dari source terbaru
php artisan view:clear --no-interaction

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
