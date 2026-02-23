
# -----------------------
# Stage 1: Build frontend (Vite)
# -----------------------
FROM node:20-bullseye AS frontend

WORKDIR /app

# Debug: نطبع إصدارات node/npm للتأكد في اللوج
RUN node -v && npm -v

# نجبر Rollup على تجاهل native binaries (قبل أي تثبيت)
ENV ROLLUP_SKIP_NATIVE=true
ENV NODE_ENV=production

# انسخ package files أولاً للحصول على cache جيد
COPY package.json package-lock.json ./

# نظف الكاش ثم تثبيت بدقة مع منع optional binaries
RUN npm cache clean --force \
 && npm ci --legacy-peer-deps --omit=optional

# انسخ باقي المشروع
COPY . .

# تبني الـ assets
RUN npm run build

# -----------------------
# Stage 2: PHP backend
# -----------------------
FROM php:8.2-fpm

WORKDIR /var/www/html

# امتدادات php المطلوبة
RUN docker-php-ext-install pdo pdo_mysql

# Composer من الصورة الرسمية
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# انسخ الكود
COPY . .

# انسخ الـ assets المبنية فقط من المرحلة الأولى
COPY --from=frontend /app/public/build ./public/build

# تثبيت PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# صلاحيات و link
RUN chown -R www-data:www-data /var/www/html \
 && chmod -R 755 /var/www/html \
 && php artisan storage:link || true

EXPOSE 8000
CMD ["php-fpm"]