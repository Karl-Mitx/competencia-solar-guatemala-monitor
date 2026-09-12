FROM node:22-alpine AS assets
WORKDIR /app
COPY package*.json ./
RUN npm ci
COPY resources ./resources
COPY vite.config.js ./
RUN npm run build

FROM php:8.3-apache AS app
RUN apt-get update && apt-get install -y --no-install-recommends libonig-dev libzip-dev libicu-dev libsqlite3-dev unzip \
    && docker-php-ext-install pdo_mysql pdo_sqlite mbstring bcmath intl zip opcache \
    && a2enmod rewrite headers \
    && rm -rf /var/lib/apt/lists/*
COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer
WORKDIR /var/www/html
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-interaction --prefer-dist --optimize-autoloader
COPY . .
COPY --from=assets /app/public/build ./public/build
COPY deploy/apache.conf /etc/apache2/sites-available/000-default.conf
RUN composer dump-autoload --no-dev --optimize \
    && mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod +x deploy/entrypoint.sh
ENV APP_ENV=production APP_DEBUG=false LOG_CHANNEL=stderr SESSION_DRIVER=file CACHE_STORE=file QUEUE_CONNECTION=sync PORT=80
EXPOSE 80
ENTRYPOINT ["/var/www/html/deploy/entrypoint.sh"]
