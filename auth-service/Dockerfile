FROM node:20-alpine AS node_builder

WORKDIR /app

COPY package*.json ./
RUN npm install

COPY . .
RUN npm run build

FROM php:8.4-cli

WORKDIR /var/www

RUN apt-get update && apt-get install -y \
    git unzip curl sqlite3 \
    libsqlite3-dev libzip-dev zip \
    libicu-dev libonig-dev libxml2-dev \
    libpng-dev libjpeg-dev libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
    pdo \
    pdo_sqlite \
    zip \
    intl \
    mbstring \
    bcmath \
    xml \
    gd \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY --from=node_builder /app /var/www

RUN composer install --no-dev --optimize-autoloader

RUN mkdir -p storage bootstrap/cache database \
    && chown -R www-data:www-data storage bootstrap/cache database \
    && chmod -R 775 storage bootstrap/cache database

EXPOSE 8000

CMD ["sh", "-c", "php artisan optimize:clear && php artisan serve --host=0.0.0.0 --port=8000"]
