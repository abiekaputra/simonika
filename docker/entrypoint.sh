#!/bin/sh
set -eu

if [ ! -f .env ]; then
    cp .env.example .env
fi

if ! grep -q '^APP_KEY=base64:' .env; then
    php artisan key:generate --force --no-interaction
fi

mkdir -p "$(dirname "${DB_DATABASE:-/var/www/html/storage/docker/database.sqlite}")"
touch "${DB_DATABASE:-/var/www/html/storage/docker/database.sqlite}"
php artisan migrate --force --no-interaction
chown -R www-data:www-data storage/docker storage/framework storage/logs bootstrap/cache

exec "$@"
