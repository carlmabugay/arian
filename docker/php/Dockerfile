FROM php:8.3-fpm-alpine

# -------------------------------------------------
# 1. System dependencies (separate, explicit)
# -------------------------------------------------
RUN apk add --no-cache \
    bash \
    curl \
    git \
    zip \
    unzip \
    nodejs \
    npm \
    libpng-dev \
    libzip-dev \
    oniguruma-dev \
    icu-dev

# -------------------------------------------------
# 2. Build dependencies (needed for PHP extensions)
# -------------------------------------------------
RUN apk add --no-cache --virtual .build-deps \
    autoconf \
    gcc \
    g++ \
    make \
    libc-dev

# -------------------------------------------------
# 3. PHP extensions (clear & isolated)
# -------------------------------------------------
RUN docker-php-ext-install \
    pdo_mysql \
    intl \
    zip \
    opcache

# -------------------------------------------------
# 4. Remove build deps (clean image)
# -------------------------------------------------
RUN apk del .build-deps

# -------------------------------------------------
# 5. Install Composer
# -------------------------------------------------
RUN curl -sS https://getcomposer.org/installer | php \
    -- --install-dir=/usr/local/bin --filename=composer

# -------------------------------------------------
# 6. App directory
# -------------------------------------------------
WORKDIR /var/www

# -------------------------------------------------
# 7. Composer dependencies
# -------------------------------------------------
COPY composer.json composer.lock ./
RUN composer install \
    --prefer-dist \
    --no-interaction \
    --optimize-autoloader \
    --no-scripts

# -------------------------------------------------
# 8. Copy Node files
# -------------------------------------------------
COPY package.json package-lock.json ./

# -------------------------------------------------
# 9. Copy app source
# -------------------------------------------------
COPY . .

# -------------------------------------------------
# Node dependencies + Vite build
RUN npm install
RUN npm run build
# -------------------------------------------------

# -------------------------------------------------
# 11. Permissions
# -------------------------------------------------
RUN chown -R www-data:www-data \
    storage \
    bootstrap/cache

# -------------------------------------------------
# 12. Run PHP-FPM
# -------------------------------------------------
EXPOSE 9000
CMD ["php-fpm"]
