FROM php:8.3-fpm-alpine

RUN apk add --no-cache git unzip icu-dev libzip-dev oniguruma-dev \
    && docker-php-ext-install intl pdo_mysql opcache

# (si tu utilises SQLite en tests, ajoute aussi:)
# RUN apk add --no-cache sqlite-dev && docker-php-ext-install pdo_sqlite

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
WORKDIR /var/www/html
