#!/usr/bin/env bash
# Exit immediately if a command exits with a non-zero status
set -o errexit

echo "==> Building Laravel Application for Render (Native Environment) <=="

# Install Composer dependencies without dev packages
composer install --no-dev --optimize-autoloader --no-interaction

# Create storage symlink
php artisan storage:link || true

# Run database migrations
if [ -n "$DATABASE_URL" ] || [ -n "$DB_HOST" ]; then
    echo "==> Running database migrations on Neon PostgreSQL..."
    php artisan migrate --force

    if [ "$RUN_SEEDER" = "true" ]; then
        echo "==> Seeding database..."
        php artisan db:seed --force
    fi
fi

# Cache config, routes, and views
echo "==> Caching config, routes, and views..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "==> Build complete! <=="
