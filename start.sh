#!/bin/bash

# Write .env file
cat > .env <<EOF
APP_NAME=${APP_NAME:-SaaSEcommerce}
APP_ENV=${APP_ENV:-production}
APP_KEY=placeholder
APP_DEBUG=${APP_DEBUG:-true}
APP_URL=${APP_URL:-https://saas-ecommerce-xx7e.onrender.com}
CENTRAL_DOMAINS=${CENTRAL_DOMAINS:-saas-ecommerce-xx7e.onrender.com}
DB_CONNECTION=${DB_CONNECTION:-pgsql}
DB_HOST=${DB_HOST:-db.jxlesdofpgavqncugjjo.supabase.co}
DB_PORT=${DB_PORT:-5432}
DB_DATABASE=${DB_DATABASE:-postgres}
DB_USERNAME=${DB_USERNAME:-postgres}
DB_PASSWORD=${DB_PASSWORD:-Shanze@7860}
DB_SSLMODE=${DB_SSLMODE:-require}
SESSION_DRIVER=${SESSION_DRIVER:-database}
CACHE_STORE=${CACHE_STORE:-database}
QUEUE_CONNECTION=${QUEUE_CONNECTION:-database}
EOF

# Always generate fresh APP_KEY (writes proper base64 key to .env)
php artisan key:generate --force

# Migrate first
php artisan migrate --force

# Cache config (now .env has valid APP_KEY)
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Start server
php artisan serve --host=0.0.0.0 --port=${PORT:-8000}
