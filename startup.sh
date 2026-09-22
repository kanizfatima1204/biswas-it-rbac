#!/bin/sh
set -e

echo "🚀 Starting Biswas IT RBAC on Railway..."

# Create required directories
mkdir -p database storage/framework/sessions storage/framework/views storage/framework/cache/data storage/logs
touch database/database.sqlite

# A Railway deployment starts from a clean checkout, where .env is deliberately
# absent. Laravel's key generator requires an APP_KEY entry to already exist.
[ -f .env ] || cp .env.example .env

# Railway's MySQL template provides MYSQL* variables. Map them when the service
# has not been configured with Laravel's DB_* variable names directly.
export DB_CONNECTION="${DB_CONNECTION:-mysql}"
export DB_HOST="${DB_HOST:-${MYSQLHOST:-${MYSQL_HOST:-127.0.0.1}}}"
export DB_PORT="${DB_PORT:-${MYSQLPORT:-${MYSQL_PORT:-3306}}}"
export DB_DATABASE="${DB_DATABASE:-${MYSQLDATABASE:-${MYSQL_DATABASE:-railway}}}"
export DB_USERNAME="${DB_USERNAME:-${MYSQLUSER:-${MYSQL_USER:-root}}}"
export DB_PASSWORD="${DB_PASSWORD:-${MYSQLPASSWORD:-${MYSQL_PASSWORD:-}}}"

echo "✅ Directories ready"

# Never reuse a configuration cache built with a previous environment.
php artisan config:clear

# Prefer the persistent Railway APP_KEY variable. If it has not been configured,
# generate a key in the deployment's .env so the app can still boot.
if [ -z "${APP_KEY:-}" ] && ! grep -q '^APP_KEY=.+' .env; then
    echo "Generating temporary APP_KEY..."
    php artisan key:generate --force
fi

# Run migrations + seed
echo "Running migrations..."
php artisan migrate --force --seed
echo "✅ Migrations complete"

# Clear and warm cache
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
