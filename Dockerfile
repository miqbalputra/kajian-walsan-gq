FROM php:8.2-fpm-alpine

# Install system dependencies and helper script
ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

RUN chmod +x /usr/local/bin/install-php-extensions && \
    apk add --no-cache git curl nginx supervisor nodejs npm mariadb-client

# Install PHP extensions using the helper script
# This script handles all necessary system dependencies automatically and is more memory efficient
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

WORKDIR /var/www/html

# Copy composer files first (for layer caching)
COPY composer.json composer.lock ./

# Install PHP dependencies (Using update to fix out-of-sync lock file)
RUN COMPOSER_MEMORY_LIMIT=-1 composer update --no-dev --optimize-autoloader --no-scripts --no-interaction --no-progress --verbose

# Copy package files and install JS dependencies
COPY package.json package-lock.json ./
RUN npm ci

# Copy application files
COPY . .

# Create necessary storage directories and set permissions
RUN mkdir -p storage/framework/cache/data \
    && mkdir -p storage/framework/app/cache \
    && mkdir -p storage/framework/sessions \
    && mkdir -p storage/framework/views \
    && mkdir -p storage/logs \
    && mkdir -p bootstrap/cache \
    && chown -R www-data:www-data /var/www/html/storage \
    && chown -R www-data:www-data /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache

# Build frontend assets
RUN npm run build

# Run composer scripts after full copy
RUN composer dump-autoload --optimize

# Copy nginx config
COPY docker/nginx.conf /etc/nginx/nginx.conf

# Copy supervisor config
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Copy entrypoint
COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

EXPOSE 80

ENTRYPOINT ["/entrypoint.sh"]
