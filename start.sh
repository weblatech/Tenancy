#!/bin/bash

# Create .env from environment variables if it doesn't exist
if [ ! -f .env ]; then
    cp .env.example .env
fi

# Generate APP_KEY if not set
if [ -z "$APP_KEY" ]; then
    php artisan key:generate --force
fi

# Cache config with actual env vars (Render sets DB_CONNECTION=pgsql at runtime)
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run migrations
php artisan migrate --force

# Start server
php artisan serve --host=0.0.0.0 --port=${PORT:-8000}
