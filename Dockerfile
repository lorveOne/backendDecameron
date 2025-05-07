# Usa la imagen oficial de PHP con FPM y Alpine (ligera)
FROM php:8.2-fpm-alpine

# Argumentos de construcción (para opciones configurables)
ARG APP_ENV=production
ARG APP_DEBUG=false

# Variables de entorno
ENV APP_ENV=${APP_ENV}
ENV APP_DEBUG=${APP_DEBUG}
ENV APP_URL=${APP_URL}
ENV APP_KEY=${APP_KEY}
ENV LOG_CHANNEL=stderr

# Dependencias del sistema
RUN apk add --no-cache \
    nginx \
    supervisor \
    libzip-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    oniguruma-dev \
    libxml2-dev \
    postgresql-dev \
    curl \
    zip \
    unzip \
    git

# Configuración de PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
    gd \
    pdo \
    pdo_pgsql \
    pgsql \
    mbstring \
    xml \
    zip \
    opcache

# Instala Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Directorio de trabajo
WORKDIR /var/www/html

# Copia solo los archivos necesarios para instalar dependencias primero
COPY composer.json composer.lock ./

# Instala dependencias (sin scripts para optimizar la construcción)
RUN if [ "$APP_ENV" = "production" ]; then \
    composer install --no-dev --no-scripts --no-interaction --optimize-autoloader; \
    else \
    composer install --no-interaction --optimize-autoloader; \
    fi

# Copia el resto de la aplicación
COPY . .

# Genera clave de aplicación si no existe
RUN if [ -z "$APP_KEY" ]; then \
    php artisan key:generate --show; \
    fi

# Optimiza para producción
RUN if [ "$APP_ENV" = "production" ]; then \
    php artisan optimize:clear && \
    php artisan optimize && \
    php artisan view:cache && \
    php artisan event:cache; \
    fi

# Permisos para Laravel
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache && \
    chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Configuración de Nginx
COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/site.conf /etc/nginx/conf.d/default.conf

# Configuración de Supervisor
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Puerto expuesto
EXPOSE 8080

# Comando de inicio
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]