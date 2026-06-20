#!/bin/bash

# Generate a fresh APP_KEY if not set
if [ -z "$APP_KEY" ]; then
    export APP_KEY=$(php artisan key:generate --show)
fi

# Write .env
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

# Wait for database (max 90s)
echo "Checking database..."
TRIES=0
DB_READY=false
until [ $TRIES -ge 45 ]; do
    if php artisan db:table sessions --quiet 2>/dev/null; then
        DB_READY=true
        echo "Database connected!"
        break
    fi
    TRIES=$((TRIES+1))
    echo "  Waiting for database... ($TRIES/45)"
    sleep 2
done

if [ "$DB_READY" = true ]; then
    php artisan migrate --force
    echo "Migrations complete."
else
    echo "Database not ready - serving with file sessions only."
fi

# Start the server (NO config:cache - let Laravel read env vars directly)
php artisan serve --host=0.0.0.0 --port=${PORT:-8000}
