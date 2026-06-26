#!/bin/sh
set -eu

envsubst '$PHP_URL' < /default.conf.template > /etc/nginx/conf.d/default.conf

if [ "${NGINX_ENABLE_HTTPS:-0}" = "1" ] || [ "${NGINX_ENABLE_HTTPS:-}" = "true" ]; then
  envsubst '$PHP_URL' < /https.conf.template > /etc/nginx/conf.d/https.conf
else
  rm -f /etc/nginx/conf.d/https.conf
fi

exec nginx -g 'daemon off;'
