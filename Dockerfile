# ===== base =====
FROM php:8.3-apache AS base

RUN apt-get update && apt-get install -y \
    libicu-dev libzip-dev unzip git \
 && docker-php-ext-install pdo pdo_mysql intl zip \
 && rm -rf /var/lib/apt/lists/*

RUN a2enmod rewrite

# Composer (depuis l'image officielle)
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Point Apache sur /public
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
 && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf \
 && sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

# ===== prod =====
FROM base AS prod

# Copie du projet
COPY . .

# Évite l'avertissement Git (optionnel)
RUN git config --global --add safe.directory /var/www/html

# Installer SANS scripts (sinon Symfony tente cache:clear, etc.)
RUN composer install --no-dev --prefer-dist --optimize-autoloader --no-interaction --no-scripts

# ✅ Assurer l'existence de var/ avant de changer les droits
RUN mkdir -p /var/www/html/var/cache /var/www/html/var/log \
    && chown -R www-data:www-data /var/www/html/var /var/www/html/public

EXPOSE 80
CMD ["apache2-foreground"]


# ===== ci/dev =====
FROM base AS ci

WORKDIR /var/www/html
COPY . .

# 1) Évite l’avertissement git
RUN git config --global --add safe.directory /var/www/html

# 2) Donne des valeurs par défaut "inoffensives" pour que Composer soit content si un script lit l'env
ENV APP_ENV=dev \
    DATABASE_URL=sqlite:////tmp/dev.db \
    MESSENGER_TRANSPORT_DSN=in-memory:// \
    MAILER_DSN=null://null \
    DEFAULT_URI=http://localhost \
    COMPOSER_ALLOW_SUPERUSER=1

# 3) Installe AVEC les dev-deps mais SANS scripts (évite cache:clear au build)
RUN composer install --no-interaction --prefer-dist --no-scripts

