FROM php:8.3-fpm-alpine

# ------------------------
# Install system dependencies
# ------------------------
RUN apk add --no-cache \
    bash \
    curl \
    git \
    zip \
    unzip \
    libpng-dev \
    libzip-dev \
    oniguruma-dev \
    icu-dev \
    nodejs \
    npm \
    autoconf \
    gcc \
    g++ \
    make \
    libc-dev

# ------------------------
# Install PHP extensions
# ------------------------
RUN docker-php-ext-install pdo_mysql intl zip opcache

# ------------------------
# Clean up build dependencies to keep image small
# ------------------------
RUN apk del autoconf gcc g++ make libc-dev

# ------------------------
# Install Composer globally
# ------------------------
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# ------------------------
# Set working directory
# ------------------------
WORKDIR /var/www

# ------------------------
# Copy application code
# ------------------------
COPY . .

# ------------------------
# Install PHP dependencies
# ------------------------
RUN composer install --prefer-dist --no-interaction --optimize-autoloader

# ------------------------
# Optional: Build frontend assets
# Uncomment if you want to pre-build during image build
# ------------------------
RUN npm install
# RUN npm run build

# ------------------------
# Set permissions for Laravel
# ------------------------
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# ------------------------
# Expose PHP-FPM port
# ------------------------
EXPOSE 9000

# ------------------------
# Start PHP-FPM
# ------------------------
CMD ["php-fpm"]
