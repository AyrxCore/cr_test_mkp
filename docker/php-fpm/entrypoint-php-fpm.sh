#!/bin/sh
set -e

echo "Run composer install..."
composer i -o
echo "Migrations for production..."
bin/console doctrine:migrations:migrate -n
bin/console d:f:l --group=dev -q

# Symfony process running as www-data will try to write within /var/www/var/log|cache
# Ensure it's owned by proper user to avoid permission issue
chown -R www-data /var/www/var/log /var/www/var/cache

# echo "Generate keys..."
# bin/console lexik:jwt:generate-keypair --overwrite -n
echo "Start PHP-FPM..."
php-fpm

exec docker-php-entrypoint "$@"
