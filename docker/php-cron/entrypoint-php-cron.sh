#!/bin/sh
set -e

echo "Run composer install..."
composer i -o
echo "Run cron in blocking mode..."
bin/console cron:start --blocking -n

exec docker-php-entrypoint "$@"