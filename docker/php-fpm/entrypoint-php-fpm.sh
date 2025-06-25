#!/bin/sh
set -e

echo "Runnning Supervisord to start php-fpm and messenger-consume..."

# Multiple cron are run: worker and (historic) cron
# Use supervisor to manage multiple process in container
# See supervisord.conf for commands run
supervisord -u root -n
