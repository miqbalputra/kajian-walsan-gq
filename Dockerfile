# ============================================================
# FrankenPHP + Laravel Octane Dockerfile
# ============================================================
# Mengganti PHP-FPM + Nginx + Supervisor dengan FrankenPHP
# (Caddy + PHP dalam satu binary, Laravel app di-keep di memory).
#
# Menggunakan Bookworm (Debian) variant — direkomendasikan oleh
# FrankenPHP docs. Alpine variant tidak include binary lengkap
# dan mendownload saat runtime (menyebabkan "no available server").
# ============================================================

FROM dunglas/frankenphp:latest-php8.2-bookworm

# Install system dependencies (Debian/Bookworm)
RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    curl \
    nodejs \
    npm \
    mariadb-client \
    supervisor \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions — split into layers for better caching.
# Extensions yang cepat (pre-compiled) dipisah dari yang compile dari source.

# Layer 1: Extensions wajib (kecil, cepat)
RUN install-php-extensions mbstring opcache pdo_mysql pcntl exif gd zip intl bcmath

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Copy composer files first (layer caching)
COPY composer.json composer.lock ./

# Install PHP dependencies dari lock file
RUN COMPOSER_MEMORY_LIMIT=-1 composer install --no-dev --optimize-autoloader --no-scripts --no-interaction --no-progress

# Copy package files and install JS dependencies
COPY package.json package-lock.json ./
RUN npm ci

# Copy application files
COPY . .

# Create necessary storage directories FIRST (needed by package:discover)
RUN mkdir -p storage/framework/cache/data \
    && mkdir -p storage/framework/app/cache \
    && mkdir -p storage/framework/sessions \
    && mkdir -p storage/framework/views \
    && mkdir -p storage/logs \
    && mkdir -p bootstrap/cache \
    && mkdir -p storage/app/livewire-tmp \
    && mkdir -p storage/app/public/attendance-proofs \
    && mkdir -p storage/app/public/izin-documents \
    && mkdir -p storage/app/public/reupload-proofs \
    && chown -R www-data:www-data /app/storage \
    && chown -R www-data:www-data /app/bootstrap/cache \
    && chmod -R 775 /app/storage /app/bootstrap/cache

# Build frontend assets
RUN npm run build

# Run composer scripts after full copy (storage dirs must exist first)
RUN composer dump-autoload --optimize

# Copy custom PHP ini (upload limits, OPcache, temp dir)
COPY docker/php-octane.ini /usr/local/etc/php/conf.d/zzz-custom.ini

# Copy supervisor config (queue worker + pulse only — FrankenPHP handles web)
COPY docker/supervisord-octane.conf /etc/supervisor/conf.d/supervisord.conf

# Copy entrypoint
COPY docker/entrypoint-octane.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

EXPOSE 80

ENTRYPOINT ["/entrypoint.sh"]