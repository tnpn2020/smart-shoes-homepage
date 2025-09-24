FROM php:8.3-fpm-alpine AS base

RUN apk add --no-cache --virtual .build-deps \
      icu-dev libzip-dev oniguruma-dev $PHPIZE_DEPS \
 && docker-php-ext-install intl mbstring zip opcache pdo_mysql \
 && apk del .build-deps

COPY php.ini /usr/local/etc/php/php.ini

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

RUN addgroup -g 1000 app && adduser -G app -u 1000 -D app
USER app

WORKDIR /var/www/html