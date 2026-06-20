FROM php:8.2-apache

# سسٹم ڈیپینڈنسیز
RUN apt-get update && apt-get install -y libpng-dev libjpeg-dev libfreetype6-dev zip unzip git \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql

# Apache کنفیگریشن
RUN a2enmod rewrite
RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf

# Composer انسٹال کریں
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# ورکنگ ڈائریکٹری اور فائلز کاپی کریں
WORKDIR /var/www/html
COPY . .

# ڈیپینڈنسیز انسٹال کریں
RUN composer install --no-interaction --no-dev --optimize-autoloader

# پرمیشنز سیٹ کریں
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# پورٹ
EXPOSE 80

# سرور سٹارٹ ہونے پر مائیگریشن چلائیں اور پھر اپاچی چلائیں
CMD bash -c "php artisan migrate --force && php artisan config:cache && apache2-foreground"