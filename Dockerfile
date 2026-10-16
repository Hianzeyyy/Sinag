# Build Laravel frontend assets
FROM node:20-alpine AS frontend

WORKDIR /app

# Changed to npm install to prevent lockfile sync errors
COPY package.json package-lock.json* ./
RUN npm install

COPY . .
RUN npm run build


# Run Laravel with PHP and Apache
FROM php:8.3-apache

ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

# Install PHP extensions
RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    unzip \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    && docker-php-ext-install pdo_mysql mbstring bcmath exif pcntl gd zip \
    && rm -rf /var/lib/apt/lists/*

# Registration accepts two verification images. Keep PHP's request limits above
# Laravel's per-file validation limit so uploads are not silently discarded.
RUN printf '%s\n' \
    'upload_max_filesize=10M' \
    'post_max_size=24M' \
    'max_file_uploads=10' \
    > /usr/local/etc/php/conf.d/99-sinag-uploads.ini

# Use one Apache MPM only. The PHP Apache base image uses prefork for mod_php;
# explicitly disable other MPMs so inherited image configuration cannot load
# multiple MPM modules.
RUN a2dismod mpm_event mpm_worker mpm_prefork 2>/dev/null || true \
    && a2enmod mpm_prefork rewrite

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy Laravel source code
COPY . .

# Install production PHP dependencies
RUN composer install \
    --no-dev \
    --prefer-dist \
    --no-interaction \
    --optimize-autoloader

# Copy built frontend assets
COPY --from=frontend /app/public/build ./public/build

# Configure Apache to serve Laravel's public directory
RUN sed -ri "s!/var/www/html!${APACHE_DOCUMENT_ROOT}!g" /etc/apache2/sites-available/*.conf \
    && sed -ri '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

# Ensure Laravel storage directories exist, then set permissions
RUN mkdir -p storage/app/public/profile-photos storage/app/private/verification/selfie \
    storage/app/private/password-reset-requests \
    storage/framework/sessions storage/framework/views storage/framework/cache storage/logs bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Render provides PORT at runtime, so configure Apache when the container starts.
CMD ["sh", "-c", "port=${PORT:-10000}; sed -ri \"s/^Listen [0-9]+/Listen ${port}/\" /etc/apache2/ports.conf; sed -ri \"s/:80>/:${port}>/\" /etc/apache2/sites-available/000-default.conf; mkdir -p storage/app/public/profile-photos storage/app/private/verification/selfie storage/app/private/password-reset-requests; chown -R www-data:www-data storage bootstrap/cache; chmod -R 775 storage bootstrap/cache; php artisan storage:link || true; php artisan app:repair-migrations-table || exit 1; php artisan migrate --force || exit 1; php artisan db:seed --force || echo 'Laravel seed skipped; Apache will still start.'; exec apache2-foreground"]
