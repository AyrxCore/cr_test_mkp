# syntax=docker/dockerfile:1
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
FROM php:8.5-fpm AS php

RUN apt-get update && \
  apt-get install -y --no-install-recommends libssl-dev zlib1g-dev curl git unzip netcat-traditional libxml2-dev libpq-dev libzip-dev libpng-dev supervisor libicu-dev && \
  pecl install apcu xdebug && \
  docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql && \
  docker-php-ext-install -j$(nproc) zip intl pdo_pgsql pgsql pcntl gd && \
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

# GitHub token to avoid HTTP 400 from codeload.github.com on anonymous zip downloads
# Pass at build time: docker build --build-arg COMPOSER_AUTH='{"github-oauth":{"github.com":"<token>"}}'
# Or set COMPOSER_AUTH env variable before running make build
# NOTE: declared as ARG only (no ENV) so the token is available to the RUN below
# but is NOT persisted into the final pushed image. With BuildKit enabled, build
# args are not stored in image metadata either.
ARG COMPOSER_AUTH=""

# Composer dependencies and public files must be owned by www-data
# Disable cache for now as current Docker install on Jenkins server is old and
# cause a directory /var/www/var/cache owned by root to remain after build
# causing crash on container start as PHP user www-data cannot write to /var/www/var/cache
# RUN --mount=type=cache,uid=33,gid=33,target=/var/www/.composer/cache \
#   --mount=type=cache,uid=33,gid=33,target=/var/www/var/cache \
#
# codeload.github.com intermittently returns HTTP 400/429 errors when
# Composer downloads too many archives in parallel (up to 12 by default).
# We limit parallelism and retry multiple times to make the build more reliable.
RUN set -eux; \
  export COMPOSER_MAX_PARALLEL_HTTP=6; \
  for attempt in 1 2 3; do \
    composer install -o --no-interaction --prefer-dist && exit 0; \
    if [ "$attempt" -lt 3 ]; then \
      echo "composer install failed (attempt $attempt/3), retrying in 10s..."; \
      sleep 10; \
    fi; \
  done; \
  echo "composer install failed after 3 attempts" >&2; \
  exit 1

# Copy node build
COPY --from=node --chown=www-data:www-data /var/www/public public/

ENTRYPOINT [ "/usr/local/bin/entrypoint-php-fpm.sh" ]

USER root

#
# NGINX
#
FROM nginx:alpine AS nginx

COPY docker/nginx/conf.d/default.conf /default.conf.template
COPY docker/nginx/conf.d/https.conf /https.conf.template
COPY docker/nginx/conf.d/fastcgi_php.conf /etc/nginx/conf.d/fastcgi_php.conf
COPY docker/nginx/docker-entrypoint-nginx.sh /docker-entrypoint-nginx.sh
RUN chmod +x /docker-entrypoint-nginx.sh

WORKDIR /var/www

COPY --from=php /var/www/public public/

CMD ["/docker-entrypoint-nginx.sh"]
