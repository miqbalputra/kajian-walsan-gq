# ============================================================
# FrankenPHP + Laravel Octane Dockerfile
# ============================================================
# Mengganti PHP-FPM + Nginx + Supervisor dengan FrankenPHP
# (Caddy + PHP dalam satu binary, Laravel app di-keep di memory).
#
# Hasil: response time turun drastis (no per-request bootstrap),
# Docker image lebih kecil, proses lebih sedikit.
# ============================================================

FROM dunglas/frankenphp:latest-php8.2-alpine

# Install system dependencies
RUN apk add --no-cache git curl nodejs npm mariadb-client supervisor

# Install PHP extensions menggunakan script bawaan FrankenPHP image
RUN install-php-extensions \
    pdo_mysql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    zip \
    intl \
    opcache \
    gd \
    redis

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

# Build frontend assets
RUN npm run build

# Run composer scripts after full copy
RUN composer dump-autoload --optimize

# Create necessary storage directories and set permissions
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

# Copy custom PHP ini (upload limits, OPcache, temp dir)
COPY docker/php-octane.ini /usr/local/etc/php/conf.d/zzz-custom.ini

# Copy supervisor config (queue worker + pulse only — FrankenPHP handles web)
COPY docker/supervisord-octane.conf /etc/supervisor/conf.d/supervisord.conf

# Copy entrypoint
COPY docker/entrypoint-octane.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

EXPOSE 80

ENTRYPOINT ["/entrypoint.sh"]