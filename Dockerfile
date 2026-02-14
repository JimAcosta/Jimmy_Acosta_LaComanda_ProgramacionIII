FROM php:8.3-cli

WORKDIR /app

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    libpq-dev \
    unzip \
    curl \
    && docker-php-ext-install pdo pdo_pgsql \
    && apt-get clean

# Copiar archivos
COPY . .

# Instalar Composer
RUN curl -sS https://getcomposer.org/installer | php
RUN php composer.phar install

# Exponer puerto (opcional pero recomendable)
EXPOSE 8080

CMD php -S 0.0.0.0:$PORT -t api

