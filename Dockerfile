# Usa la imagen oficial de PHP con Alpine (ligera)
FROM php:8.2-cli-alpine

# Argumentos de construcción
ARG APP_ENV=production
ARG APP_DEBUG=false

# Variables de entorno
ENV APP_ENV=${APP_ENV}
ENV APP_DEBUG=${APP_DEBUG}
ENV APP_URL=${APP_URL}
ENV APP_KEY=${APP_KEY}
ENV PORT=8080
ENV HOST=0.0.0.0

# Dependencias del sistema
RUN apk add --no-cache \
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

# Instala dependencias
RUN if [ "$APP_ENV" = "production" ]; then \
    composer install --no-dev --no-scripts --no-interaction --optimize-autoloader; \
    else \
    composer install --no-interaction --optimize-autoloader; \
    fi

# Copia el resto de la aplicación
COPY . .



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

# Puerto expuesto
EXPOSE ${PORT}

# Comando de inicio - Usa el servidor built-in de PHP
CMD php artisan serve --host=${HOST} --port=${PORT}