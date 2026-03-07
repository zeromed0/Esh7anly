
FROM php:8.2-apache

# تثبيت النظام + node
RUN apt-get update && apt-get install -y \
    libzip-dev \
    unzip \
    git \
    libpq-dev \
    curl \
    gnupg \
    && curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && docker-php-ext-install pdo pdo_pgsql zip \
    && a2enmod rewrite

WORKDIR /var/www/html

# composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# نسخ package files أولاً (لتسريع build)
COPY package*.json ./

RUN npm install

# نسخ باقي المشروع
COPY . .

# تثبيت Laravel
RUN composer install --no-dev --optimize-autoloader

# build الواجهة
RUN npm run build

# الصلاحيات
RUN chown -R www-data:www-data storage bootstrap/cache \
 && chmod -R 775 storage bootstrap/cache

# جعل apache يشير إلى public
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|' /etc/apache2/sites-available/000-default.conf

# cache laravel
RUN php artisan config:cache \
 && php artisan route:cache \
 && php artisan view:cache

CMD ["apache2-foreground"]