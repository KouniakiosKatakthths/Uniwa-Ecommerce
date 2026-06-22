FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
    git curl libpng-dev libonig-dev libxml2-dev \
    libzip-dev zip unzip nodejs npm \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip
RUN a2enmod rewrite headers remoteip

# create a new linux user group called 'developer' with an arbitrary group id of '1001'
RUN groupadd -g 1001 developer
# create a new user called developer and add it to this group
RUN useradd -u 1001 -g developer developer

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

RUN composer install --optimize-autoloader --no-dev --no-interaction

RUN npm ci && npm run build && rm -rf node_modules

COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

EXPOSE 80

USER www-data
ENTRYPOINT ["/entrypoint.sh"]

CMD ["apache2-foreground"]