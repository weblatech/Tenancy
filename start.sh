#!/bin/bash

echo "=== Starting SaaS Ecommerce ==="

# Use APP_KEY from Render env vars, or generate one
if [ -z "$APP_KEY" ]; then
    APP_KEY=$(php artisan key:generate --show 2>/dev/null)
    if [ -z "$APP_KEY" ]; then
        echo "ERROR: Could not generate APP_KEY"
        exit 1
    fi
    export APP_KEY
fi

echo "APP_KEY is set (${#APP_KEY} chars)"

# Write .env
cat > .env <<EOF
APP_NAME=SaaSEcommerce
APP_ENV=local
APP_KEY=${APP_KEY}
APP_DEBUG=true
APP_URL=https://saas-ecommerce-xx7e.onrender.com
CENTRAL_DOMAINS=saas-ecommerce-xx7e.onrender.com
DB_CONNECTION=pgsql
DB_HOST=${DB_HOST:-}
DB_PORT=${DB_PORT:-5432}
DB_DATABASE=${DB_DATABASE:-}
DB_USERNAME=${DB_USERNAME:-}
DB_PASSWORD=${DB_PASSWORD:-}
DB_SSLMODE=prefer
SESSION_DRIVER=database
SESSION_CONNECTION=pgsql
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=lax
SESSION_PATH=/
CACHE_STORE=file
QUEUE_CONNECTION=database
LOG_CHANNEL=stack
LOG_LEVEL=debug
META_APP_ID=${META_APP_ID:-}
META_APP_SECRET=${META_APP_SECRET:-}
META_EMBEDDED_SIGNUP_CONFIG_ID=${META_EMBEDDED_SIGNUP_CONFIG_ID:-}
EOF

# Fix storage permissions at runtime
mkdir -p storage/framework/sessions storage/framework/views storage/framework/cache storage/logs
chmod -R 777 storage 2>/dev/null
chmod -R 777 bootstrap/cache 2>/dev/null

# Verify storage is writable
touch storage/framework/sessions/test.txt 2>/dev/null
if [ $? -eq 0 ]; then
    echo "Storage is writable"
    rm -f storage/framework/sessions/test.txt
else
    echo "ERROR: Storage is NOT writable"
    exit 1
fi

# ALWAYS clear cached config - stale cache causes 419
rm -f bootstrap/cache/config.php 2>/dev/null
rm -f bootstrap/cache/routes-*.php 2>/dev/null
rm -f bootstrap/cache/services.php 2>/dev/null
php artisan config:clear 2>/dev/null
php artisan route:clear 2>/dev/null
php artisan cache:clear 2>/dev/null
php artisan view:clear 2>/dev/null
echo "All caches cleared"

# Run migrations with retry
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

# Cache views only
php artisan view:cache 2>/dev/null

echo "Starting server on port ${PORT:-8000}..."
# Start the queue worker in background (processes WhatsApp messages, etc.)
php artisan queue:work --sleep=3 --tries=3 --max-time=3600 &
echo "Queue worker started in background"

# Start the server
php artisan serve --host=0.0.0.0 --port=${PORT:-8000}
