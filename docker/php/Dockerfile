FROM php:7.4 as php

COPY --from=composer:2.2.4 /usr/bin/composer /usr/local/bin/composer

WORKDIR /app

FROM php as dev

ARG PROJECT_PATH_RELATIVE_TO_ROOT

COPY ${PROJECT_PATH_RELATIVE_TO_ROOT}/docker/php/config/php-dev.ini /usr/local/etc/php/conf.d/99-dev.ini

RUN \
    pecl install xdebug \
    && docker-php-ext-enable xdebug \
    && docker-php-ext-install pcntl

# Redirect Composer cache to the host
ENV COMPOSER_CACHE_DIR=/app/${PROJECT_PATH_RELATIVE_TO_ROOT}/var/composer
