FROM php:8.2-fpm-alpine

# Install system dependencies
RUN apk add --no-cache \
    git \
    curl \
    libpng-dev \
    libzip-dev \
    zip \
    unzip \
    nginx \
    supervisor \
    nodejs \
    npm \
    mariadb-dev \
    mariadb-client \
    oniguruma-dev \
    freetype-dev \
    libjpeg-turbo-dev \
    icu-dev

# Install PHP extensions (Split to reduce RAM usage during build)
RUN docker-php-ext-install pdo pdo_mysql \
    && docker-php-ext-install mbstring exif pcntl bcmath zip opcache intl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy composer files first (for layer caching)
COPY composer.json composer.lock ./

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-scripts --no-interaction

# Copy package files and install JS dependencies
COPY package.json package-lock.json ./
RUN npm ci

# Copy application files
COPY . .

# Build frontend assets
RUN npm run build

# Run composer scripts after full copy
RUN composer dump-autoload --optimize

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# Copy nginx config
COPY docker/nginx.conf /etc/nginx/nginx.conf

# Copy supervisor config
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Copy entrypoint
COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

EXPOSE 80

ENTRYPOINT ["/entrypoint.sh"]
