FROM composer:2 AS vendor

WORKDIR /app
COPY . .
RUN composer install --no-dev --no-interaction --prefer-dist --no-progress --optimize-autoloader

FROM php:8.3-apache

RUN apt-get update \
    && apt-get install -y --no-install-recommends libonig-dev libsqlite3-dev \
    && docker-php-ext-install mbstring pdo_mysql pdo_sqlite \
    && a2enmod rewrite \
    && rm -rf /var/lib/apt/lists/*

ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

WORKDIR /var/www/html
COPY . .
COPY --from=vendor /app/vendor ./vendor

RUN mkdir -p storage/framework/{cache,sessions,views} storage/logs storage/docker bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache

COPY docker/entrypoint.sh /usr/local/bin/simonika-entrypoint
RUN chmod +x /usr/local/bin/simonika-entrypoint

ENTRYPOINT ["simonika-entrypoint"]
CMD ["apache2-foreground"]
