#!/bin/bash

set -x

envsubst < /docker-utilities/phpini/php.ini.template > /usr/local/etc/php/conf.d/php.ini

# Generate .env from template with envVars
envsubst < /docker-utilities/app.env.template > /var/www/app/.env
# Configure .env.local.php (performance) after create .env:
php -d memory_limit=-1 ${COMPOSER_PATH} dump-env ${APP_ENV} --no-cache

# Install dependencies:
php -d memory_limit=-1 ${COMPOSER_PATH} install -d /var/www/app --optimize-autoloader --prefer-dist --no-progress --no-interaction --no-scripts

# Generate jwt keys:
php bin/console lexik:jwt:generate-keypair --overwrite

echo "[ENTRYPOINT] add xdebug pcov php.ini package"
envsubst < /docker-utilities/phpini/xdebug.ini.template > /usr/local/etc/php/conf.d/xdebug.ini.disable
cp /docker-utilities/phpini/pcov.ini /usr/local/etc/php/conf.d/pcov.ini.disable

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
echo "[ENTRYPOINT] running docker local entrypoint ... [/usr/local/bin/docker-php-entrypoint $@]"
echo "[ENTRYPOINT] ------------------------------------------------------------"
echo ""

exec /usr/local/bin/docker-php-entrypoint "$@"