#!/bin/bash

# Create .env file from env vars
echo "APP_NAME=${APP_NAME:-SaaSEcommerce}" > .env
echo "APP_ENV=${APP_ENV:-production}" >> .env
echo "APP_KEY=${APP_KEY:-}" >> .env
echo "APP_DEBUG=${APP_DEBUG:-true}" >> .env
echo "APP_URL=${APP_URL:-https://saas-ecommerce-xx7e.onrender.com}" >> .env
echo "CENTRAL_DOMAINS=${CENTRAL_DOMAINS:-saas-ecommerce-xx7e.onrender.com}" >> .env
echo "DB_CONNECTION=${DB_CONNECTION:-pgsql}" >> .env
echo "DB_HOST=${DB_HOST:-db.jxlesdofpgavqncugjjo.supabase.co}" >> .env
echo "DB_PORT=${DB_PORT:-5432}" >> .env
echo "DB_DATABASE=${DB_DATABASE:-postgres}" >> .env
echo "DB_USERNAME=${DB_USERNAME:-postgres}" >> .env
echo "DB_PASSWORD=${DB_PASSWORD:-Shanze@7860}" >> .env
echo "DB_SSLMODE=${DB_SSLMODE:-require}" >> .env
echo "SESSION_DRIVER=${SESSION_DRIVER:-database}" >> .env
echo "CACHE_STORE=${CACHE_STORE:-database}" >> .env
echo "QUEUE_CONNECTION=${QUEUE_CONNECTION:-database}" >> .env

# Generate APP_KEY if empty
if [ -z "$APP_KEY" ]; then
    php artisan key:generate --force
fi

# Migrate first (so session/cache tables exist)
php artisan migrate --force

# Then cache config
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Start server
php artisan serve --host=0.0.0.0 --port=${PORT:-8000}
