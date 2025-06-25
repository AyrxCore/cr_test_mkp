# syntax=docker/dockerfile:1-labs
#
# Intermediate image to build public directory
#
FROM node:lts-alpine AS node

WORKDIR /var/www

COPY package.json yarn.lock ./
RUN set -eux; yarn install && yarn cache clean

COPY . ./

RUN yarn build

CMD ["yarn"]

#
# PHP FPM
#
FROM php:8.3-fpm AS php

RUN apt-get update && \
  apt-get install -y --no-install-recommends libssl-dev zlib1g-dev curl git unzip netcat-traditional libxml2-dev libpq-dev libzip-dev libpng-dev supervisor && \
  pecl install apcu xdebug && \
  docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql && \
  docker-php-ext-install -j$(nproc) zip opcache intl pdo_pgsql pgsql pcntl gd && \
  docker-php-ext-enable apcu pdo_pgsql sodium xdebug && \
  apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# INSTALL COMPOSER
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY docker/php-fpm/php.ini /usr/local/etc/php/php.ini
COPY docker/php-fpm/mkp.conf /usr/local/etc/php-fpm.d/www.conf

# ENTRYPOINT SCRIPT FOR PHP-FPM
COPY docker/php-fpm/entrypoint-php-fpm.sh /usr/local/bin/entrypoint-php-fpm.sh
COPY docker/php-fpm/command-php-fpm.sh /usr/local/bin/command-php-fpm.sh
RUN chmod +x /usr/local/bin/entrypoint-php-fpm.sh
RUN chmod +x /usr/local/bin/command-php-fpm.sh

# ENTRYPOINT SCRIPT FOR CRON
COPY docker/php-cron/entrypoint-php-cron.sh /usr/local/bin/entrypoint-php-cron.sh
RUN chmod +x /usr/local/bin/entrypoint-php-cron.sh

# SUPERVISOR CONFIGURATION AND SCRIPTS
COPY docker/php-fpm/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

COPY docker/php-fpm/supervisor/run-php-fpm.sh /usr/local/bin/run-php-fpm.sh
RUN chmod +x /usr/local/bin/run-php-fpm.sh

COPY docker/php-fpm/supervisor/run-messenger-consume.sh /usr/local/bin/run-messenger-consume.sh
RUN chmod +x /usr/local/bin/run-messenger-consume.sh

# Prepare /var/www directory with expected structure and permissions
WORKDIR /var/www
RUN mkdir -p /var/www/var/log && \
  chown -R www-data:www-data /var/www

USER www-data

COPY --chown=www-data:www-data . ./

# Composer dependencies and public files must be owned by www-data
# Disable cache for now as current Docker install on Jenkins server is old and 
# cause a directory /var/www/var/cache owned by root to remain after build
# causing crash on container start as PHP user www-data cannot write to /var/www/var/cache
# RUN --mount=type=cache,uid=33,gid=33,target=/var/www/.composer/cache \
#   --mount=type=cache,uid=33,gid=33,target=/var/www/var/cache \
RUN composer i -o

# Copy node build
COPY --from=node --chown=www-data:www-data /var/www/public public/

ENTRYPOINT [ "/usr/local/bin/entrypoint-php-fpm.sh" ]

USER root

#
# NGINX
#
FROM nginx:alpine AS nginx

COPY docker/nginx/conf.d/default.conf /default.conf.template

WORKDIR /var/www

COPY --from=php /var/www/public public/

CMD ["/bin/sh" , "-c" , "envsubst '$PHP_URL' < /default.conf.template > /etc/nginx/conf.d/default.conf && exec nginx -g 'daemon off;'"]
