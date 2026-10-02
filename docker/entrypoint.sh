#!/bin/bash
set -e

# Default PORT if not provided by Render
PORT=${PORT:-8080}
echo "==> Configuring Nginx to listen on port ${PORT}..."
sed -i "s/PORT_PLACEHOLDER/${PORT}/g" /etc/nginx/http.d/default.conf

# Ensure storage and database directories exist and have proper permissions
mkdir -p /var/www/html/storage/framework/cache/data \
         /var/www/html/storage/framework/sessions \
         /var/www/html/storage/framework/views \
         /var/www/html/storage/logs \
         /var/www/html/bootstrap/cache \
         /var/www/html/database

chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database

# Create storage symlink
php artisan storage:link --force || true

# Validate or auto-generate APP_KEY if missing or invalid
echo "==> Verifying application encryption key (APP_KEY)..."
VALID_KEY=$(php -r '
require "vendor/autoload.php";
try {
    $key = getenv("APP_KEY");
    if (empty($key)) throw new Exception("Empty");
    $app = require_once "bootstrap/app.php";
    $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
    app("encrypter");
    echo "VALID";
} catch (\Throwable $e) {
    echo "INVALID";
}
')

if [ "$VALID_KEY" != "VALID" ]; then
    echo "==> APP_KEY missing or invalid. Auto-generating fresh encryption key..."
    GEN_KEY=$(php artisan key:generate --show)
    export APP_KEY="$GEN_KEY"
    echo "APP_KEY=$GEN_KEY" >> /var/www/html/.env
    echo "==> Application Key successfully initialized."
fi

# Database connection handling
if [ -n "$DATABASE_URL" ] || [ -n "$DB_HOST" ]; then
    echo "==> Database target: Neon PostgreSQL"
    export DB_CONNECTION=pgsql

    echo "==> Running database migrations on PostgreSQL..."
    MIGRATE_SUCCESS=false
    for i in 1 2 3 4 5; do
        if php artisan migrate --force; then
            MIGRATE_SUCCESS=true
            echo "==> Migrations completed successfully."
            break
        else
            echo "==> Migration attempt $i failed. Retrying in 3 seconds..."
            sleep 3
        fi
    done

    if [ "$MIGRATE_SUCCESS" = "true" ]; then
        if [ "$RUN_SEEDER" = "true" ] || [ -z "$RUN_SEEDER" ]; then
            echo "==> Seeding database..."
            php artisan db:seed --force || echo "==> Notice: Seeder completed or already populated."
        fi
    else
        echo "==> Warning: Remote migrations failed after 5 attempts. Check DATABASE_URL credentials."
    fi
else
    echo "==> Notice: Neither DATABASE_URL nor DB_HOST is set."
    echo "==> Falling back to SQLite local database to ensure website loads immediately..."
    export DB_CONNECTION=sqlite
    export DB_DATABASE=/var/www/html/database/database.sqlite
    touch /var/www/html/database/database.sqlite
    chown www-data:www-data /var/www/html/database/database.sqlite
    chmod 664 /var/www/html/database/database.sqlite

    php artisan migrate --force
    php artisan db:seed --force
fi

# Cache configuration, routes, and views for production performance
echo "==> Optimizing Laravel cache (config, route, view)..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "==> Starting web service with Supervisor on port ${PORT}..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
