FROM php:8.2-cli

# Install system deps + Node.js
RUN apt-get update && apt-get install -y \
    git curl zip unzip libpng-dev libonig-dev libxml2-dev libpq-dev \
    && docker-php-ext-install pdo_pgsql mbstring exif pcntl bcmath gd \
    && curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-scripts

COPY package.json package-lock.json* ./
RUN npm install

COPY . .

RUN npm run build

RUN rm -f bootstrap/cache/config.php \
    && rm -f bootstrap/cache/routes-v7.php \
    && rm -f bootstrap/cache/views.php \
    && rm -f .env

RUN chmod +x start.sh

EXPOSE 8000

CMD ["./start.sh"]
