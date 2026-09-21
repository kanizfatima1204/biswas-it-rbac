#!/bin/sh
set -e

# Create required directories
mkdir -p database storage/framework/sessions storage/framework/views storage/framework/cache/data storage/logs

# Generate .env dynamically using Railway-injected MySQL variables
cat > .env <<EOF
APP_NAME="Biswas IT Firm RBAC"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://${RAILWAY_PUBLIC_DOMAIN:-localhost}

LOG_CHANNEL=stack
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=${MYSQLHOST:-127.0.0.1}
DB_PORT=${MYSQLPORT:-3306}
DB_DATABASE=${MYSQLDATABASE:-railway}
DB_USERNAME=${MYSQLUSER:-root}
DB_PASSWORD=${MYSQLPASSWORD:-}

SESSION_DRIVER=file
SESSION_LIFETIME=120
SESSION_ENCRYPT=false

CACHE_STORE=file
QUEUE_CONNECTION=sync

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local

MAIL_MAILER=log
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="Biswas IT Firm RBAC"
EOF

echo "✅ .env created"

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
php artisan storage:link --force
echo "✅ Cache warmed"

# Start server
exec php artisan serve --host=0.0.0.0 --port=${PORT:-8000}
