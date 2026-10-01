# Imagen de demostración: Apache + PHP 8.4 con SQLite.
# La base de datos se recrea con datos de ejemplo en cada arranque (ver deploy/entrypoint.sh).

# --- Assets (Vite) ---
FROM node:22-alpine AS assets
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci --ignore-scripts
COPY vite.config.js postcss.config.js tailwind.config.js ./
COPY resources ./resources
RUN npm run build

# --- Aplicación ---
FROM php:8.4-apache

COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
RUN install-php-extensions intl zip bcmath gd opcache \
    && a2enmod rewrite \
    && sed -i 's#/var/www/html#/var/www/html/public#g' /etc/apache2/sites-available/000-default.conf \
    && mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY composer.json composer.lock ./
# Se instalan también las dependencias de desarrollo: los seeders de la demo usan Faker.
RUN composer install --no-scripts --no-autoloader --no-interaction --prefer-dist

COPY . .
COPY --from=assets /app/public/build ./public/build
RUN composer dump-autoload --optimize \
    && chown -R www-data:www-data storage bootstrap/cache database \
    && chmod +x deploy/entrypoint.sh

ENV APP_NAME="Clínica Veterinaria" \
    APP_ENV=production \
    APP_DEBUG=false \
    LOG_CHANNEL=stderr \
    DB_CONNECTION=sqlite \
    DB_DATABASE=/var/www/html/database/database.sqlite \
    SESSION_DRIVER=database \
    CACHE_STORE=database \
    QUEUE_CONNECTION=sync \
    MAIL_MAILER=log \
    RUN_SCHEDULER=true \
    DEMO_SEEDERS=ExampleScenarioSeeder \
    CLINIC_OPERATING_DAYS=1,2,3,4,5,6,7 \
    CLINIC_OPERATING_START=06:00 \
    CLINIC_OPERATING_END=20:00

EXPOSE 8080
CMD ["deploy/entrypoint.sh"]
