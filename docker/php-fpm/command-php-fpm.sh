#!/bin/sh
set -e

# Load fixtures and install dev deps for dev only
if [ "$APP_ENV" = "dev" ]; then
    echo "Running for dev: APP_ENV=$APP_ENV, setting dev config..."

    # This is already run at build time for prod deps
    # APP_ENV=dev will cause additional dev deps to be installed
    composer i -o
fi

echo "Migrations for production..."
bin/console doctrine:migrations:migrate -n

# echo "Generate keys..."
# bin/console lexik:jwt:generate-keypair --overwrite -n

echo "Start PHP-FPM..."
php-fpm
