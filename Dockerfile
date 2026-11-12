FROM php:8.3-cli-bookworm AS php-base

RUN apt-get update \
    && apt-get install -y --no-install-recommends libonig-dev libpq-dev libzip-dev \
    && docker-php-ext-install mbstring pdo_pgsql zip \
    && rm -rf /var/lib/apt/lists/*

FROM php-base AS build

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

ARG APP_PATH

COPY ${APP_PATH}/ ./

RUN composer install \
    --no-interaction \
    --prefer-dist \
    --optimize-autoloader \
    && mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache

FROM php-base AS runtime

WORKDIR /var/www/html

COPY --from=build --chown=www-data:www-data /var/www/html /var/www/html

USER www-data

EXPOSE 8000

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
