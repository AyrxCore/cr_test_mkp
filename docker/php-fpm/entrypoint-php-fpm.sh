#!/bin/sh
set -e

echo "Run composer install..."
composer i -o
echo "Migrations for production..."
bin/console doctrine:migrations:migrate -n
# echo "Generate keys..."
# bin/console lexik:jwt:generate-keypair --overwrite -n
echo "Start PHP-FPM..."
php-fpm

exec docker-php-entrypoint "$@"