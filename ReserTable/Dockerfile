# Dockerfile optimizado para Render
FROM php:8.2-fpm

# Establece el directorio de trabajo
WORKDIR /var/www/html

# Instala dependencias del sistema necesarias + Node.js
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev \
    libjpeg-dev \
    libfreetype6-dev \
    nodejs \
    npm \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Instala Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copia todos los archivos del proyecto
COPY . /var/www/html

# Instala las dependencias de Composer
RUN composer install --no-interaction --optimize-autoloader --no-dev

# Instala dependencias Node y compila assets
RUN npm ci && npm run build

# Crea los directorios necesarios si no existen
RUN mkdir -p /var/www/html/storage /var/www/html/bootstrap/cache

# Ajusta permisos
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Expone el puerto que Render espera
EXPOSE $PORT

# Comando optimizado para Render
CMD php artisan migrate --force && php artisan config:cache && php artisan route:cache && php artisan view:cache && php artisan serve --host=0.0.0.0 --port=$PORT