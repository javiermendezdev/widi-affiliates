
# Colors:
# reset = 0,
# letter color without background => black = 30, red = 31, green = 32, yellow = 33, blue = 34, magenta = 35, cyan = 36, and white=37
# letter white and background => black = 40, red = 41, green = 42, yellow = 43, blue = 44, magenta = 45, cyan = 46, and white=47

# VAR := value
APP_NAME := widi-affiliates
DOCKER_COMPOSE_ALL := -f docker-compose.databases.yml -f docker-compose.utils.yml -f docker-compose.yml

default:
	@printf "$$HELP"


# Principals:
install: build/image-base create/docker-env configure/docker build/databases build/utils build/app
up:
	docker-compose ${DOCKER_COMPOSE_ALL} up -d \
	&& docker-compose ${DOCKER_COMPOSE_ALL} logs -f
stop:
	docker-compose ${DOCKER_COMPOSE_ALL} stop
test:
	docker-compose exec ${APP_NAME} php -d memory_limit=-1 vendor/bin/simple-phpunit --testdox
logs:
	docker-compose logs -f --tail 100 ${APP_NAME}
docker-enter:
	docker-compose exec ${APP_NAME} /bin/bash

docker-enter-root:
	docker-compose exec --user=0 ${APP_NAME} /bin/bash

# Docker:
create/docker-env:
	@if [ ! -f .env ]; then cp .env.example .env; echo "Environment .env created"; fi
configure/docker:
	docker network ls|grep ${APP_NAME} > /dev/null || docker network create ${APP_NAME}
build/image-base:
	docker build -t javiermendezdev/php8-apache:base .
build/app:
	docker-compose up --build -d
build/databases:
	docker-compose -f docker-compose.databases.yml up --build -d
build/utils:
	docker-compose -f docker-compose.utils.yml up --build -d

# Composer
composer/dump-autoload:
	@docker-compose exec ${APP_NAME} php -d memory_limit=-1 /usr/bin/composer dump-autoload --apcu --optimize
composer/install:
	@docker-compose exec ${APP_NAME} php -d memory_limit=-1 /usr/bin/composer install -d /var/www/app --optimize-autoloader --prefer-dist --no-progress --no-interaction --no-scripts
# composer/fund:
# 	@docker-compose exec ${APP_NAME} php -d memory_limit=-1 /usr/bin/composer fund

# Symfony:
symfony/bin-console:
	docker-compose exec ${APP_NAME} php bin/console ${command}
symfony/cache-clear:
	docker-compose exec ${APP_NAME} php bin/console cache:clear
symfony/env-vars:
	docker-compose exec ${APP_NAME} php bin/console debug:container --env-vars
symfony/doctrine-diff:
	@docker-compose exec ${APP_NAME} php -d memory_limit=-1 bin/console doctrine:cache:clear-metadata --env=dev --no-debug
	@docker-compose exec ${APP_NAME} php -d memory_limit=-1 bin/console doctrine:migrations:diff --env=dev --no-debug
symfony/doctrine-migrate:
	@docker-compose exec ${APP_NAME} php -d memory_limit=-1 bin/console doctrine:migrations:migrate --env=dev --no-debug
symfony/doctrine-schema-validate:
	@docker-compose exec ${APP_NAME} php -d memory_limit=-1 bin/console doctrine:schema:validate --env=dev --no-debug

# Fixtures:
fixtures/load:
	@docker-compose exec ${APP_NAME} php -d memory_limit=-1 bin/console hautelook:fixtures:load --env=dev --no-debug

# phpini packages
phpini/package-info:
	@docker-compose exec ${APP_NAME} php -i | grep ${package}
phpini/xdebug-enable:
	@docker-compose exec ${APP_NAME} rm -rf /usr/local/etc/php/conf.d/pcov.ini
	@docker-compose exec ${APP_NAME} cp /usr/local/etc/php/conf.d/xdebug.ini.disable /usr/local/etc/php/conf.d/xdebug.ini
phpini/xdebug-disable:
	@docker-compose exec ${APP_NAME} rm -rf /usr/local/etc/php/conf.d/xdebug.ini
phpini/pcov-enable:
	@docker-compose exec ${APP_NAME} rm -rf /usr/local/etc/php/conf.d/xdebug.ini
	@docker-compose exec ${APP_NAME} cp /usr/local/etc/php/conf.d/pcov.ini.disable /usr/local/etc/php/conf.d/pcov.ini
phpini/pcov-disable:
	@docker-compose exec ${APP_NAME} rm -rf /usr/local/etc/php/conf.d/pcov.ini

# Phpunit
test/coverage: phpini/pcov-enable
	docker-compose exec ${APP_NAME} php -d memory_limit=-1 -d xdebug.mode=coverage vendor/bin/simple-phpunit --testdox --coverage-text
test/coverage-html: phpini/pcov-enable
	docker-compose exec ${APP_NAME} php -d memory_limit=-1 -d xdebug.mode=coverage vendor/bin/simple-phpunit --testdox --coverage-html ./tests/Reports/Html

define HELP

  \e[1;35mList commands\e[0m:

	- make install \t Install the project
	- make up \t Launch the project
	- make stop \t Stop dockers
	- make symfony/bin-console command='???' \t Execute symfony command (Example: 'make symfony/bin-console command=make:controller')

  \e[1;33mPlease execute "make <command>". Example make hello\e[0m


endef

export HELP