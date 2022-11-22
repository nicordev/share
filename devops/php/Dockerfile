FROM php:8.1-fpm-alpine

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# https://getcomposer.org/doc/03-cli.md#composer-allow-superuser
ENV COMPOSER_ALLOW_SUPERUSER=1

ENV PATH="${PATH}:/root/.composer/vendor/bin"

RUN set -eux; \
    apk add --no-cache --virtual .build-deps  \
        $PHPIZE_DEPS \
        icu-dev \
        libzip-dev \
        libpq-dev \
        postgresql-dev \
        bash \
    ; \
    docker-php-ext-install -j$(nproc) \
        intl \
        zip \
        pdo \
        pdo_pgsql \
    ; \
    pecl install xdebug; \
    apk del .build-deps

COPY ./devops/php/conf.d/90-xdebug.ini $PHP_INI_DIR/conf.d/90-xdebug.ini

WORKDIR /srv/app
