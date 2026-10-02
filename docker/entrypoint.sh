#!/bin/bash
set -e

# Default PORT if not provided by Render
PORT=${PORT:-8080}
echo "==> Configuring Nginx to listen on port ${PORT}..."
sed -i "s/PORT_PLACEHOLDER/${PORT}/g" /etc/nginx/http.d/default.conf

# Ensure storage directories exist and have proper permissions
mkdir -p /var/www/html/storage/framework/cache/data \
         /var/www/html/storage/framework/sessions \
         /var/www/html/storage/framework/views \
         /var/www/html/storage/logs \
         /var/www/html/bootstrap/cache

chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Create storage symlink
php artisan storage:link --force || true

# Check database connection and run migrations
if [ -n "$DATABASE_URL" ] || [ -n "$DB_HOST" ]; then
    echo "==> Running database migrations on Neon PostgreSQL..."
    php artisan migrate --force

    # Seed data if RUN_SEEDER is set to true
    if [ "$RUN_SEEDER" = "true" ]; then
        echo "==> Running database seeders..."
        php artisan db:seed --force
    fi
else
    echo "==> Notice: Neither DATABASE_URL nor DB_HOST is set. Skipping remote migration."
fi

# Cache configuration, routes, and views for production performance
echo "==> Optimizing Laravel cache (config, route, view)..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "==> Starting web service with Supervisor on port ${PORT}..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
