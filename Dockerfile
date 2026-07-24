# Forzar reconstruccion limpia (cambia la fecha si se desea)
ARG CACHE_BUST=20250320

# Etapa 1: Compilar frontend con Vite
FROM node:20-alpine AS frontend
WORKDIR /app
COPY package*.json ./
RUN npm install
COPY . .
RUN npm run build

# Etapa 2: Instalar dependencias PHP con Composer (oficial)
FROM php:8.4-cli-alpine AS composer
RUN apk add --no-cache git unzip libpng-dev libjpeg-turbo-dev freetype-dev libzip-dev
RUN docker-php-ext-configure gd --with-freetype --with-jpeg && docker-php-ext-install gd zip
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
WORKDIR /app
COPY . .
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Etapa 3: Servidor PHP de produccion (PHP 8.4)
FROM php:8.4-fpm-alpine

# Instalar dependencias del sistema y soporte para PostgreSQL/Excel
RUN apk add --no-cache nginx postgresql-dev libpng-dev libjpeg-turbo-dev freetype-dev libzip-dev
RUN docker-php-ext-configure gd --with-freetype --with-jpeg && docker-php-ext-install pdo pdo_pgsql gd zip

WORKDIR /var/www/html
COPY . .

# Copiar frontend compilado (Vite)
COPY --from=frontend /app/public/build ./public/build

# Copiar vendor generado por Composer
COPY --from=composer /app/vendor ./vendor

# Correccion de permisos
RUN mkdir -p storage/logs && \
    touch storage/logs/laravel.log && \
    chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache && \
    chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Configuracion de Nginx
COPY ./nginx.conf /etc/nginx/nginx.conf

# Script de entrada
COPY ./entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 80
CMD ["sh", "/usr/local/bin/entrypoint.sh"]
