FROM php:8.3-fpm-alpine

# 1. System dependencies
RUN apk add --no-cache \
    bash curl git zip unzip nodejs npm libpng-dev libzip-dev oniguruma-dev icu-dev

# 2. Build dependencies
RUN apk add --no-cache --virtual .build-deps \
    autoconf gcc g++ make libc-dev

# 3. PHP extensions
RUN docker-php-ext-install pdo_mysql intl zip opcache

# 4. Remove build dependencies
RUN apk del .build-deps

# 5. Composer
RUN curl -sS https://getcomposer.org/installer | php \
    -- --install-dir=/usr/local/bin --filename=composer

# 6. App directory
WORKDIR /var/www

# 7. Copy composer and install
COPY composer.json composer.lock ./
RUN composer install --prefer-dist --no-interaction --optimize-autoloader --no-scripts

# 8. Copy Node files and install
COPY package.json package-lock.json ./
RUN npm install

# 9. Copy full app
COPY . .

# 10. Build assets into shared volume
RUN npm run build

# 11. Permissions
RUN chown -R www-data:www-data storage bootstrap/cache public

# 12. Expose PHP-FPM port
EXPOSE 9000
CMD ["php-fpm"]
