# Imagen base oficial de PHP con Apache
FROM php:8.2-apache

# 1. Instalar dependencias del sistema y extensiones PHP (MySQL, PostgreSQL, GD, ZIP, etc.)
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype-dev \
    libonig-dev \
    libxml2-dev \
    libpq-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    curl \
  && docker-php-ext-configure gd \
       --with-freetype=/usr/include/ \
       --with-jpeg=/usr/include/ \
  && docker-php-ext-install \
       pdo_mysql \
       pdo_pgsql \
       pgsql \
       mbstring \
       exif \
       pcntl \
       bcmath \
       gd \
       zip

# 2. Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 3. Definir directorio de trabajo
WORKDIR /var/www/html

# 4. Copiar el código del proyecto (incluye .env.example)
COPY . .

# 5. Copiar .env.example a .env si no existe; luego generar APP_KEY
RUN cp .env.example .env \
  && php artisan key:generate --ansi

# 6. Instalar dependencias de PHP con Composer
RUN composer install --no-dev --optimize-autoloader

# 7. Ajustar permisos en storage y cache
RUN chown -R www-data:www-data storage bootstrap/cache \
  && chmod -R 775 storage bootstrap/cache

# 8. Configurar Apache para servir desde public/ y habilitar mod_rewrite
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e "s!/var/www/html!${APACHE_DOCUMENT_ROOT}!g" /etc/apache2/sites-available/*.conf \
  && sed -ri -e "s!/var/www/html!${APACHE_DOCUMENT_ROOT}!g" /etc/apache2/apache2.conf \
  && a2enmod rewrite \
  && sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

# 9. Variables de entorno por defecto para Postgres (se pueden sobreescribir en runtime)
ENV DB_CONNECTION=pgsql \
    DB_HOST=dpg-d0dbkvruibrs73f5s0bg-a.oregon-postgres.render.com \
    DB_PORT=5432 \
    DB_DATABASE=hoteles_bz83 \
    DB_USERNAME=hoteles_bz83_user \
    DB_PASSWORD=Ltfnvte01T8VCdyEPVITLv4x7CziD8Y4 \
    DB_SSLMODE=require

# 10. Exponer el puerto 80 y arrancar Apache
EXPOSE 80
CMD ["apache2-foreground"]

