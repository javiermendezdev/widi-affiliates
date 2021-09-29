#!/bin/bash

#set -x

# Generate .env from template with envVars
envsubst < /docker-utilities/app.env.template > /var/www/app/.env
# Configure .env.local.php (performance) after create .env:
php -d memory_limit=-1 ${COMPOSER_PATH} dump-env ${APP_ENV} --no-cache

# TODO: move to docker secret, don't generate in entrypoint
# Generate jwt keys:
php bin/console lexik:jwt:generate-keypair --overwrite

if [ "$APP_EXECUTE_MIGRATIONS" = "true" ]; then

    #echo "[ENTRYPOINT] Update mongodb schema:"
    #php /var/www/app/bin/console doctrine:mongodb:schema:update --no-interaction
    # if [ $? -ne 0 ]; then
    #     echo "[ENTRYPOINT] ERROR: Mongodb Schema update can't be executed." > /dev/stderr
    #     exit 1
    # fi

    echo "[ENTRYPOINT] Execute migrations"
 	php /var/www/app/bin/console doctrine:migrations:migrate --no-interaction
 	if [ $? -ne 0 ]; then
        echo "[ENTRYPOINT] ERROR: Migrations can't be executed." > /dev/stderr
        exit 1
    fi
fi

# Run entrypoint
echo "[ENTRYPOINT] running docker base entrypoint ... [/usr/local/bin/docker-php-entrypoint $@]"
echo "[ENTRYPOINT] ------------------------------------------------------------"
echo ""

exec /usr/local/bin/docker-php-entrypoint "$@"