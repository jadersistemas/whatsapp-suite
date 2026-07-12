# syntax=docker/dockerfile:1.7

ARG NODE_VERSION=20
ARG PHP_VERSION=8.2

# Stage 1: Build frontend assets
FROM node:${NODE_VERSION}-alpine AS frontend

WORKDIR /app

COPY package.json package-lock.json* ./
RUN npm ci --no-audit --no-fund

COPY . .
RUN npm run build

# Stage 2: PHP-FPM runtime
FROM php:${PHP_VERSION}-fpm-alpine

# Install system dependencies + PHP extensions
RUN apk add --no-cache \
    libzip-dev \
    oniguruma-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    icu-dev \
    libpq-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_pgsql bcmath gd intl opcache pcntl

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Install PHP dependencies first (cache layer)
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

# Copy application code
COPY . .

# Copy built frontend assets from stage 1
COPY --from=frontend /app/public/build ./public/build

# Run post-install scripts (package:discover, etc.)
RUN composer dump-autoload --optimize --no-dev

# Set permissions for storage and cache
RUN chown -R www-data:www-data /var/www \
    && chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# Copy custom php.ini
COPY docker/php.ini /usr/local/etc/php/conf.d/app.ini

EXPOSE 9000

CMD ["php-fpm"]
