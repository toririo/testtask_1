FROM php:8-fpm-alpine

RUN apk add --no-cache linux-headers  \
    && apk add --update --no-cache --virtual .build-dependencies $PHPIZE_DEPS \
    && docker-php-ext-install mysqli pdo pdo_mysql \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug \
    && pecl clear-cache \
    && apk del .build-dependencies
