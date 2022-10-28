FROM node:lts-alpine AS node

WORKDIR /var/www

COPY . ./
RUN set -eux; \
    yarn install; \
    yarn cache clean;

RUN yarn build

CMD ["yarn"]

FROM php:8.0-fpm AS php

COPY docker/php-fpm/wait-for-it.sh /usr/bin/wait-for-it
RUN chmod +x /usr/bin/wait-for-it

# ENTRYPOINT SCRIPT FOR PHP-FPM
COPY docker/php-fpm/entrypoint-php-fpm.sh /usr/local/bin/entrypoint-php-fpm.sh
RUN chmod +x /usr/local/bin/entrypoint-php-fpm.sh
# ENTRYPOINT SCRIPT FOR CRON
COPY docker/php-cron/entrypoint-php-cron.sh /usr/local/bin/entrypoint-php-cron.sh
RUN chmod +x /usr/local/bin/entrypoint-php-cron.sh


RUN apt-get update && \
    apt-get install -y --no-install-recommends libssl-dev zlib1g-dev curl git unzip netcat libxml2-dev libpq-dev libzip-dev && \
    pecl install apcu xdebug && \
    docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql && \
    docker-php-ext-install -j$(nproc) zip opcache intl pdo_pgsql pgsql pcntl && \
    docker-php-ext-enable apcu pdo_pgsql sodium xdebug && \
    apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# INSTALL COMPOSER
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY docker/php-fpm/php.ini /usr/local/etc/php/php.ini

WORKDIR /var/www

COPY . ./

COPY --from=node /var/www/public public/

FROM nginx:alpine AS nginx

COPY docker/nginx/conf.d/default.conf /default.conf.template

WORKDIR /var/www

COPY --from=php /var/www/public public/

CMD ["/bin/sh" , "-c" , "envsubst '$PHP_URL' < /default.conf.template > /etc/nginx/conf.d/default.conf && exec nginx -g 'daemon off;'"]
