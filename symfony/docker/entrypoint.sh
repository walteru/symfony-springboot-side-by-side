#!/bin/bash
set -e

cd /var/www/html

if [ ! -f vendor/autoload.php ]; then
    echo "[entrypoint] vendor/ not found, running composer install..."
    composer install --no-interaction --prefer-dist --optimize-autoloader
fi

mkdir -p var/cache var/log

# Sprint 2: preparar el esquema y los datos en MySQL.
# depends_on espera a mysql "healthy", pero igual reintentamos por robustez.
if [ -n "$DATABASE_URL" ]; then
    echo "[entrypoint] esperando a la base de datos..."
    for i in $(seq 1 30); do
        if php bin/console dbal:run-sql "SELECT 1" >/dev/null 2>&1; then
            echo "[entrypoint] base de datos lista."
            break
        fi
        echo "[entrypoint] base de datos no lista todavía ($i/30)..."
        sleep 2
    done

    # Idempotente: crea/actualiza el esquema y siembra solo si la tabla está vacía.
    php bin/console doctrine:schema:update --force --complete --no-interaction
    php bin/console app:seed --no-interaction
fi

chown -R www-data:www-data var vendor 2>/dev/null || true

exec "$@"
