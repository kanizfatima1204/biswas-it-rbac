#!/bin/sh
set -e

echo "🚀 Starting Biswas IT RBAC on Railway..."

# Create required directories
mkdir -p database storage/framework/sessions storage/framework/views storage/framework/cache/data storage/logs
touch database/database.sqlite
[ -f .env ] || touch .env

echo "✅ Directories ready"

# Ensure APP_KEY is present
if [ -z "$APP_KEY" ]; then
    echo "Generating temporary APP_KEY..."
    php artisan key:generate --force
fi

# Run migrations + seed
echo "Running migrations..."
php artisan migrate --force --seed
echo "✅ Migrations complete"

# Clear and warm cache
php artisan config:clear
php artisan route:clear
php artisan view:clear

php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan storage:link --force || true
echo "✅ Cache warmed"

# Start server in foreground
echo "🌐 Starting server on port ${PORT:-8000}..."
exec php artisan serve --host=0.0.0.0 --port="${PORT:-8000}"
