#!/bin/bash
set -e

cd /var/www/html

if [ ! -f vendor/autoload.php ]; then
    echo "[entrypoint] vendor/ not found, running composer install..."
    composer install --no-interaction --prefer-dist --optimize-autoloader
fi

mkdir -p var/cache var/log
chown -R www-data:www-data var vendor 2>/dev/null || true

exec "$@"
