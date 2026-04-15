#!/bin/sh
set -e

# Copy .env jika belum ada
if [ ! -f /var/www/html/.env ]; then
    cp /var/www/html/.env.example /var/www/html/.env
fi

# Masuk ke direktori app
cd /var/www/html

# Publish & Migrate Pulse
php artisan vendor:publish --tag=pulse-config --tag=pulse-dashboard --tag=pulse-migrations --force -n
php artisan migrate --force --no-interaction

# Cache for performance
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Pastikan direktori upload ada (Livewire temp + public subfolders)
# Livewire v3 dengan disk=local menyimpan di storage/app/private/livewire-tmp
mkdir -p /var/www/html/storage/app/livewire-tmp
mkdir -p /var/www/html/storage/app/private/livewire-tmp
mkdir -p /var/www/html/storage/app/public/attendance-proofs
mkdir -p /var/www/html/storage/app/public/izin-documents
mkdir -p /var/www/html/storage/app/public/reupload-proofs

# Pastikan /tmp writable untuk PHP upload_tmp_dir
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