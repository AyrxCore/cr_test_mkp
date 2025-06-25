#! /usr/bin/env bash

# Load fixtures and install dev deps for dev only
# It's required to run composer install again for dev deps
# as composer install during image build only includes prod deps
if [ "$APP_ENV" = "dev" ]; then
    echo "Installing dev dependencies..."
    composer i -o
fi

echo "Running migrations..."

bin/console doctrine:migrations:migrate -n

if [ $? -ne 0 ]; then
    echo "Migration script returned non-zero exit code. Exiting..."
    exit 1
fi

echo "Starting php-fpm..."

php-fpm --nodaemonize
