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
SESSION_DOMAIN=null
SESSION_PATH=/
CACHE_STORE=file
QUEUE_CONNECTION=database
EOF

# Run migrations with retry (database might take time to be ready)
echo "Running migrations..."
TRIES=0
until [ $TRIES -ge 15 ]; do
    if php artisan migrate --force 2>/dev/null; then
        echo "Migrations complete!"
        break
    fi
    TRIES=$((TRIES+1))
    echo "  Migration attempt $TRIES/15 failed, retrying in 5s..."
    sleep 5
done

if [ $TRIES -ge 15 ]; then
    echo "WARNING: Migrations failed after 15 attempts"
fi

# Cache views
php artisan view:cache 2>/dev/null

# Start the server
php artisan serve --host=0.0.0.0 --port=${PORT:-8000}
