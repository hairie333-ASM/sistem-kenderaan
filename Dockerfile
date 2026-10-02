FROM php:8.4-fpm-alpine

# Install system dependencies, root certificates, PostgreSQL client libraries, and Nginx/Supervisor
RUN apk update && apk add --no-cache \
    bash \
    curl \
    ca-certificates \
    nginx \
    supervisor \
    postgresql-client \
    postgresql-dev \
    libzip-dev \
    icu-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    oniguruma-dev \
    && update-ca-certificates

# Install required PHP extensions for Laravel & PostgreSQL
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo \
        pdo_pgsql \
        pgsql \
        bcmath \
        opcache \
        intl \
        zip \
        gd \
        mbstring

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy composer definition files
COPY composer.json composer.lock* ./

# Install composer dependencies
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader --no-scripts

# Copy application source code
COPY . .

# Complete composer autoload generation
RUN composer dump-autoload --optimize --no-dev

# Copy docker configurations
COPY docker/nginx.conf /etc/nginx/http.d/default.conf
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh

# Ensure entrypoint is executable and directory permissions are correct
RUN chmod +x /usr/local/bin/entrypoint.sh \
    && mkdir -p /var/www/html/database \
    && chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database

# Expose default port (Render will override with $PORT)
EXPOSE 8080

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
