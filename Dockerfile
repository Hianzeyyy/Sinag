# Build Laravel frontend assets
FROM node:20-alpine AS frontend

WORKDIR /app

COPY package.json package-lock.json ./
RUN npm ci

COPY . .
RUN npm run build


# Run Laravel with PHP and Apache
FROM php:8.3-apache

ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

# Install PHP extensions and configure Apache MPM
RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    unzip \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    && docker-php-ext-install pdo_mysql mbstring bcmath exif pcntl gd zip \
    && a2dismod mpm_event mpm_worker 2>/dev/null || true

RUN a2enmod mpm_prefork rewrite \
    && rm -rf /var/lib/apt/lists/*

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
RUN sed -ri "s!/var/www/html!${APACHE_DOCUMENT_ROOT}!g" \
    /etc/apache2/sites-available/*.conf \
    && sed -ri \
    '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' \
    /etc/apache2/apache2.conf \
    && chown -R www-data:www-data storage bootstrap/cache

EXPOSE 80

# Ensure only one MPM is enabled before starting Apache
CMD ["sh", "-c", "a2dismod mpm_event mpm_worker >/dev/null 2>&1 || true; rm -f /etc/apache2/mods-enabled/mpm_event.* /etc/apache2/mods-enabled/mpm_worker.*; a2enmod mpm_prefork >/dev/null 2>&1; apache2ctl -t && exec apache2-foreground"]
```
