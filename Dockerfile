FROM php:8.2-fpm

# Instala dependências do sistema
RUN apt-get update \
    && apt-get install -y \
        git \
        curl \
        libpng-dev \
        libonig-dev \
        libxml2-dev \
        zip \
        unzip \
        libzip-dev \
        libpq-dev \
        libjpeg-dev \
        libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql gd zip

# Instala Composer
COPY --from=composer:2.5 /usr/bin/composer /usr/bin/composer

# Instala Node.js
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs

# Define diretório de trabalho
WORKDIR /var/www

# Copia arquivos da aplicação
COPY . .

# Instala dependências do Laravel
RUN composer install --no-interaction --prefer-dist --optimize-autoloader \
    && if [ -f package.json ]; then npm install && npm run build; fi

# Permissões
RUN chown -R www-data:www-data /var/www \
    && chmod -R 755 /var/www/storage

# Exposição de porta do PHP-FPM
EXPOSE 9000

CMD ["php-fpm"]
