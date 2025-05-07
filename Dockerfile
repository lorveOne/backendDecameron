# Imagen base oficial de PHP con Apache
FROM php:8.2-apache

# Instalar extensiones necesarias
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    curl \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copiar los archivos del proyecto
COPY . /var/www/html

# Dar permisos y limpiar caché
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage

# Habilitar mod_rewrite
RUN a2enmod rewrite

# Cambiar directorio de trabajo
WORKDIR /var/www/html

# Exponer el puerto usado por Apache
EXPOSE 80
