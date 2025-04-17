# Usa una imagen base de PHP
FROM php:8.2-fpm

# Establece el directorio de trabajo
WORKDIR /var/www/html

# Instala dependencias del sistema necesarias
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
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Instala Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copia los archivos y directorios necesarios de la aplicación
COPY composer.json composer.lock ./ 
COPY database ./database
COPY public ./public
COPY resources ./resources
COPY routes ./routes
COPY app ./app
COPY bootstrap ./bootstrap
COPY config ./config
COPY artisan ./

# Instala las dependencias de Composer
RUN composer install --no-interaction --optimize-autoloader --no-dev

# Crea los directorios necesarios si no existen
RUN mkdir -p /var/www/html/storage /var/www/html/bootstrap/cache

# Ajusta permisos
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Expone el puerto 8000 y inicia el servidor PHP (aunque el comando se sobrescribe en docker-compose.yml)
EXPOSE 8000
# CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]