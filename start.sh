#!/bin/bash

# Generate a fresh APP_KEY if not set
if [ -z "$APP_KEY" ]; then
    export APP_KEY=$(php artisan key:generate --show)
fi

# Write .env from Render's env vars (NO hardcoded defaults for DB)
cat > .env <<EOF
APP_NAME=${APP_NAME:-SaaSEcommerce}
APP_ENV=${APP_ENV:-production}
APP_KEY=${APP_KEY}
APP_DEBUG=${APP_DEBUG:-true}
APP_URL=${APP_URL:-https://saas-ecommerce-xx7e.onrender.com}
CENTRAL_DOMAINS=${CENTRAL_DOMAINS:-saas-ecommerce-xx7e.onrender.com}
DB_CONNECTION=${DB_CONNECTION:-pgsql}
DB_HOST=${DB_HOST:-}
DB_PORT=${DB_PORT:-5432}
DB_DATABASE=${DB_DATABASE:-}
DB_USERNAME=${DB_USERNAME:-}
DB_PASSWORD=${DB_PASSWORD:-}
DB_SSLMODE=${DB_SSLMODE:-prefer}
SESSION_DRIVER=file
CACHE_STORE=file
QUEUE_CONNECTION=database
EOF

# Wait for database to be ready (max 60 seconds)
echo "Waiting for database..."
TRIES=0
until php artisan db:monitor 2>/dev/null || [ $TRIES -eq 30 ]; do
    echo "  Attempt $((TRIES+1))/30 - database not ready yet, waiting 2s..."
    sleep 2
    TRIES=$((TRIES+1))
done

# Run migrations if database is available
if [ $TRIES -lt 30 ]; then
    echo "Database is ready! Running migrations..."
    php artisan migrate --force
else
    echo "Database not ready after 60s, skipping migrations"
fi

# Cache config, routes, views
php artisan config:cache 2>/dev/null
php artisan route:cache 2>/dev/null
php artisan view:cache 2>/dev/null

# Start the server
php artisan serve --host=0.0.0.0 --port=${PORT:-8000}
