
FROM php:8.2-fpm-alpine

# Install system dependencies
RUN apk add --no-cache \
    nginx \
    nodejs \
    npm \
    git \
    curl \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    icu-dev \
    libzip-dev \
    postgresql-dev \
    sqlite-dev

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd pdo_mysql pdo_pgsql zip bcmath exif pcntl opcache intl

# Set working directory
WORKDIR /app

# Copy composer.lock and composer.json
COPY composer.json composer.lock ./

# Install Composer dependencies
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && composer install --no-dev --optimize-autoloader --no-interaction

# Copy application code
COPY . .

# Install Node.js dependencies and build assets
RUN npm install && npm run build

# Generate application key
RUN php artisan key:generate --force

# Configure Nginx
COPY docker/nginx/nginx.conf /etc/nginx/http.d/default.conf

# Expose port 8000
EXPOSE 8000

# Start PHP-FPM and Nginx
CMD sh -c "php-fpm && nginx -g 'daemon off;'"
