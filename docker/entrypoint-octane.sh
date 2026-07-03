#!/bin/sh
set -e

# ============================================================
# FrankenPHP + Octane Entry Point
# ============================================================

# Copy .env jika belum ada
if [ ! -f /app/.env ]; then
    cp /app/.env.example /app/.env
fi

cd /app

# Buat direktori yang dibutuhkan FrankenPHP/Caddy
mkdir -p /data/caddy
mkdir -p /config/caddy

# Bersihkan cache lama dari image/container sebelumnya
rm -f /app/bootstrap/cache/*.php 2>/dev/null || true
rm -f /app/storage/framework/views/*.php 2>/dev/null || true
php -r "if (function_exists('opcache_reset')) { opcache_reset(); }" 2>/dev/null || true
php artisan optimize:clear --no-interaction || true

# Generate key jika belum ada
php artisan key:generate --no-interaction --force

# Jalankan migrations (tidak menghapus data, hanya menjalankan yang pending)
php artisan migrate --force --no-interaction || echo "WARNING: Migration failed, continuing..."

# Publish Pulse config & dashboard (jika belum ada)
php artisan vendor:publish --provider="Laravel\Pulse\PulseServiceProvider" --no-interaction 2>/dev/null || true

# Hapus compiled views agar Blade/Blaze compile ulang dari source terbaru
php artisan view:clear --no-interaction || true

# Cache config & route untuk production
php artisan config:cache || echo "WARNING: config:cache failed"
php artisan route:cache || echo "WARNING: route:cache failed"

# Event cache (mempercepat event listener resolution)
php artisan event:cache 2>/dev/null || true

# Pastikan direktori upload ada
mkdir -p /app/storage/app/public/attendance-proofs
mkdir -p /app/storage/app/public/izin-documents
mkdir -p /app/storage/app/public/reupload-proofs
mkdir -p /app/storage/app/livewire-tmp

# Pastikan /tmp writable
chmod 1777 /tmp 2>/dev/null || true

# Pastikan storage link ada
php artisan storage:link --force 2>/dev/null || true

# Set permissions
chown -R www-data:www-data /app/storage /app/bootstrap/cache 2>/dev/null || true
chmod -R 775 /app/storage /app/bootstrap/cache 2>/dev/null || true

# Buat log dir untuk supervisor
mkdir -p /var/log/supervisor

# ============================================================
# Jalankan:
# 1. Supervisor (queue worker + pulse) di background
# 2. FrankenPHP + Octane server di foreground
# ============================================================

# Start supervisor di background
/usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf 2>/dev/null &

# Tunggu supervisor ready
sleep 1

# Cek apakah FrankenPHP tersedia
echo "=== Checking FrankenPHP ==="
which frankenphp 2>/dev/null && echo "FrankenPHP binary found" || echo "FrankenPHP binary not in PATH (using PHP extension instead)"
php -m 2>/dev/null | grep -i franken && echo "FrankenPHP PHP extension loaded" || echo "FrankenPHP PHP extension not found"

# Start FrankenPHP via Octane (foreground)
echo "Starting FrankenPHP + Octane server on 0.0.0.0:80..."
exec php artisan octane:frankenphp --host=0.0.0.0 --port=80