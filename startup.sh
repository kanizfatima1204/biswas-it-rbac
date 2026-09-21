#!/bin/bash
set -e

echo "🚀 Starting Biswas IT RBAC..."

# Create required directories
mkdir -p database storage/framework/sessions storage/framework/views storage/framework/cache/data storage/logs
echo "✅ Directories ready"

# Railway MySQL service injects vars as MYSQLHOST or MYSQL_HOST — support both
DB_HOST_VAL="${MYSQLHOST:-${MYSQL_HOST:-127.0.0.1}}"
DB_PORT_VAL="${MYSQLPORT:-${MYSQL_PORT:-3306}}"
DB_NAME_VAL="${MYSQLDATABASE:-${MYSQL_DATABASE:-railway}}"
DB_USER_VAL="${MYSQLUSER:-${MYSQL_USER:-root}}"
DB_PASS_VAL="${MYSQLPASSWORD:-${MYSQL_PASSWORD:-}}"
APP_URL_VAL="https://${RAILWAY_PUBLIC_DOMAIN:-localhost}"

# Generate .env dynamically
cat > .env <<ENVEOF
APP_NAME="Biswas IT Firm RBAC"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=${APP_URL_VAL}

LOG_CHANNEL=stderr
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=${DB_HOST_VAL}
DB_PORT=${DB_PORT_VAL}
DB_DATABASE=${DB_NAME_VAL}
DB_USERNAME=${DB_USER_VAL}
DB_PASSWORD=${DB_PASS_VAL}

SESSION_DRIVER=cookie
SESSION_LIFETIME=120
SESSION_ENCRYPT=false

CACHE_STORE=array
QUEUE_CONNECTION=sync

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local

MAIL_MAILER=log
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="Biswas IT Firm RBAC"
ENVEOF

echo "✅ .env generated (DB_HOST=${DB_HOST_VAL})"

# Generate app key
php artisan key:generate --force
echo "✅ APP_KEY generated"

# Run migrations + seed
php artisan migrate --force --seed
echo "✅ Migrations complete"

# Cache for performance
php artisan config:cache
php artisan route:cache
php artisan view:cache
echo "✅ Cache warmed"

# Start server
echo "🌐 Starting server on port ${PORT:-8000}..."
exec php artisan serve --host=0.0.0.0 --port="${PORT:-8000}"
