FROM php:8.2-apache

RUN a2enmod rewrite

# Dependencias del sistema
RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip \
    nodejs \
    npm \
    libzip-dev \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libicu-dev \
    libonig-dev \
    libxml2-dev \
    && rm -rf /var/lib/apt/lists/*

# Extensiones PHP
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

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

# eliminar .env del repo
RUN rm -f .env

# instalar dependencias Laravel
RUN composer install \
    --no-dev \
    --optimize-autoloader \
    --no-interaction \
    --prefer-dist

# dependencias frontend
RUN npm install

# compilar Vite
RUN npm run build

# configurar Apache para Laravel
RUN sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf \
 && sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf \
 && echo 'ServerName localhost' > /etc/apache2/conf-available/servername.conf \
 && a2enconf servername

# permisos Laravel
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# errores PHP
RUN echo "display_errors=On" >> /usr/local/etc/php/php.ini \
 && echo "display_startup_errors=On" >> /usr/local/etc/php/php.ini \
 && echo "error_reporting=E_ALL" >> /usr/local/etc/php/php.ini \
 && echo "log_errors=On" >> /usr/local/etc/php/php.ini

EXPOSE 80

CMD sh -c "
echo '===== LARAVEL START ====='
cd /var/www/html

php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

php artisan storage:link || true

php artisan migrate --force --no-interaction || true

echo '===== CREATE ADMIN USER ====='

php -r \"
require 'vendor/autoload.php';
\$app = require 'bootstrap/app.php';
\$kernel = \$app->make(Illuminate\\\\Contracts\\\\Console\\\\Kernel::class);
\$kernel->bootstrap();

if(!App\\\\Models\\\\User::where('email','admin@deskcir.com')->exists()){
    App\\\\Models\\\\User::create([
        'name' => 'Admin',
        'email' => 'admin@deskcir.com',
        'password' => Illuminate\\\\Support\\\\Facades\\\\Hash::make('Admin12345'),
        'role_id' => 1
    ]);
    echo 'ADMIN CREATED';
}else{
    echo 'ADMIN EXISTS';
}
\"

php artisan config:cache
php artisan route:cache
php artisan view:cache

echo '===== APACHE START ====='

apache2-foreground
"