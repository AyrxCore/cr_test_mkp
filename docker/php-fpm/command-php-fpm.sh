#!/bin/sh
set -e

composer i -o

echo "Migrations for production..."
bin/console doctrine:migrations:migrate -n

# Multiple cron are run: worker and (historic) cron
# Use supervisor to manage multiple process in container
# See supervisord.conf for commands run
supervisord -u root

echo "Start PHP-FPM..."
php-fpm
