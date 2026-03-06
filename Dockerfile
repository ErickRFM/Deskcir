FROM php:8.2-apache

RUN a2enmod rewrite

RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip \
    libzip-dev \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libicu-dev \
    libonig-dev \
    libxml2-dev \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo \
        pdo_mysql \
        zip \
        xml \
        gd \
        intl \
        bcmath \
        exif \
        opcache

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

# instalar dependencias
RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

# configurar apache
RUN sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf \
    && sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf \
    && echo 'ServerName localhost' > /etc/apache2/conf-available/servername.conf \
    && a2enconf servername

# permisos laravel
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# activar errores php
RUN echo "display_errors = On" >> /usr/local/etc/php/php.ini \
 && echo "display_startup_errors = On" >> /usr/local/etc/php/php.ini \
 && echo "error_reporting = E_ALL" >> /usr/local/etc/php/php.ini \
 && echo "log_errors = On" >> /usr/local/etc/php/php.ini

EXPOSE 80

CMD ["sh", "-c", "\
echo '===== LARAVEL DEBUG START ====='; \
cd /var/www/html; \
php artisan config:clear; \
php artisan cache:clear; \
php artisan route:clear; \
php artisan view:clear; \
php artisan storage:link || true; \
php artisan migrate --force --no-interaction || true; \
echo '===== STARTING APACHE ====='; \
apache2-foreground"]