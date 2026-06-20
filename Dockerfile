FROM php:8.2-apache

# سسٹم ڈیپینڈنسیز
RUN apt-get update && apt-get install -y libpng-dev libjpeg-dev libfreetype6-dev zip unzip git \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql

# Apache کنفیگریشن (DocumentRoot کو public پر سیٹ کرنا)
RUN a2enmod rewrite
RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf

# Composer انسٹال کریں
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# پروجیکٹ فائلز کاپی کریں
WORKDIR /var/www/html
COPY . .

# Composer کے ذریعے ڈیپینڈنسیز انسٹال کریں (یہی وہ لائن ہے جو vendor فولڈر بنائے گی)
RUN composer install --no-interaction --no-dev --optimize-autoloader

# پرمیشنز
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 80