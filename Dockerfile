# PHP کا ورژن منتخب کریں
FROM php:8.2-apache

# ضروری ایکسٹینشنز انسٹال کریں
RUN apt-get update && apt-get install -y libpng-dev libjpeg-dev libfreetype6-dev zip unzip git \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql

# Apache کو کنفیگر کریں
RUN a2enmod rewrite
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# پروجیکٹ کی فائلیں کاپی کریں
COPY . /var/www/html
WORKDIR /var/www/html

# پرمیشنز سیٹ کریں
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# پورٹ سیٹ کریں
EXPOSE 80