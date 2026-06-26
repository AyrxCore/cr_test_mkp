dc = docker compose -f docker-compose.yml
dc_exec_opts =
dc_exec = $(dc) exec $(dc_exec_opts)
# Used to keep only the targets passed after the last one and use them as arguments
# Example:
# make xdebug behat features/auth/api/login.feature
# $(args) will be "behat features/auth/api/login.feature" in the xdebug target
# $(args) will be "features/auth/api/login.feature" in the behat target
args = $(shell echo $(MAKECMDGOALS) | sed 's/^.*$@//')

APP_ENV ?= dev
APP_DEBUG ?= 1

##
## Docker

.env.local:
	@touch -a .env.local

dump-config: ## Dumps the docker compose config
	$(dc) config

generate-certs: ## Generate local SSL certificates via mkcert (requires: brew install mkcert)
	@which mkcert > /dev/null || (echo "❌ mkcert not found. Run: brew install mkcert" && exit 1)
	@mkdir -p docker/nginx/certs
	mkcert -install
	mkcert -key-file docker/nginx/certs/localhost-key.pem -cert-file docker/nginx/certs/localhost.pem localhost marketplace.qantis.local 127.0.0.1 ::1
	@echo "✅ Certificats générés dans docker/nginx/certs/"

init: ## Initialize docker development environment
	make up
	make database-create
	make database-update
	make database-migrations
	make init-tests
	make generate-keypair
	make build-front

build: .env.local ## Build container: make build SERVICE
	$(dc) build --pull --no-cache $(args)

pull: .env.local ## Pull new images of docker containers
	$(dc) pull $(args)

up: .env.local ## Creates and starts all containers
	APP_ENV=$(APP_ENV) APP_DEBUG=$(APP_DEBUG) MKP_GIT_TAG=$(shell git rev-parse --abbrev-ref HEAD) $(dc) up -d $(args)

stop: ## Stop containers: make stop [SERVICE]
	APP_ENV=$(APP_ENV) APP_DEBUG=$(APP_DEBUG) $(dc) stop $(args)

.PHONY: restart
restart: ## Restart containers: make restart [SERVICE]
	(make stop $(args)) || true
	APP_ENV=$(APP_ENV) APP_DEBUG=$(APP_DEBUG) $(dc) up -d $(args)

down: ## Destroy all containers
	$(dc) down

run: ## Run containers: make run SERVICE [COMMAND] [ARGS...]
	APP_ENV=$(APP_ENV) APP_DEBUG=$(APP_DEBUG) $(dc) run --rm $(args)

exec: ## Execute command in container: make exec SERVICE COMMAND [ARGS...]
	$(dc_exec) $(args)

logs: ## Displays container logs: make logs SERVICE
	APP_ENV=$(APP_ENV) APP_DEBUG=$(APP_DEBUG) $(dc) logs -f --tail 100 $(args)

php-bash:
	docker exec -it marketplace-php-1 bash

node-bash:
	docker exec -it marketplace-js-1 sh
##
## App

.PHONY: xdebug
xdebug: ## Trigger xdebug, usage example: make xdebug behat <file>
	$(eval dc_exec_opts += -e XDEBUG_TRIGGER=1)
	@echo Will trigger xdebug

.PHONY: env-test
env-test: ## Set APP_ENV variable to test for commands
	$(eval dc_exec_opts += -e APP_ENV=test)
	@echo Using test environment

composer.lock: composer.json
	$(dc_exec) php composer update --no-interaction --optimize-autoloader

composer-install: composer.lock ## Install composer dependencies
	$(dc_exec) php composer install --no-interaction --optimize-autoloader

cache-clear: ## Clear symfony cache
	$(dc_exec) php bin/console cache:clear

database-reset: database-create database-fixtures ## Reset database and load fixtures

database-create:
	$(dc_exec) php bin/console doctrine:database:create --if-not-exists
	$(dc_exec) php bin/console doctrine:schema:drop --force
	$(dc_exec) php bin/console doctrine:schema:create

database-update:
	$(dc_exec) php bin/console doctrine:schema:update --force

database-fixtures:
#	$(dc_exec) php rm -rf public/cache/*
#	$(dc_exec) php rm -rf public/images/*
#	$(dc_exec) php chown -R www-data:www-data public/cache
#	$(dc_exec) php chown -R www-data:www-data public/images
	$(dc_exec) php bin/console doctrine:fixtures:load --no-interaction -q

database-diff: ## Create doctrine migration from database diff
	$(dc_exec) php bin/console doctrine:migration:diff
	$(dc_exec) php vendor/bin/php-cs-fixer fix migrations
.PHONY: diff

database-migrations: ## Run doctrine migrations
	$(dc_exec) php bin/console doctrine:migration:migrate -n
.PHONY: migration

generate-keypair: ## Generate private/public keys
	$(dc_exec) php bin/console lexik:jwt:generate-keypair --skip-if-exists

generate-hosts: ## Generate the list of channels' hosts to be added to the /etc/hosts file
	$(dc_exec) php bin/console generate:hosts

##
## Fixtures generation
fixtures-factory: ## Generate a fixtures Factory in \App\DataFixtures\Factory
	$(dc_exec) php bin/console make:factory

##
## Front
build-front: ## Build front environment
	$(dc_exec) js yarn install
	$(dc_exec) js yarn build

##
## Tests

dc_exec_php_test = docker exec marketplace-php-1 php
dc_run_php_test = $(dc_exec_php_test) vendor/bin/pest
dc_test = docker compose -f docker-compose.test.yml

init-tests: ## Initialize test environment
	$(dc_test) up -d
	until docker exec postgres_test_mkp pg_isready -U postgres > /dev/null 2>&1; do sleep 1; done
	$(dc_exec_php_test) bin/console doctrine:database:create --if-not-exists -e test
	$(dc_exec_php_test) bin/console doctrine:schema:drop --force -e test
	$(dc_exec_php_test) bin/console doctrine:schema:update --force -e test
	$(dc_exec_php_test) bin/console -e test dbal:run-sql "ALTER DATABASE template_db_test IS_TEMPLATE true;"

.PHONY: test
all-tests:
	make init-tests
	make cache-clear-test
	$(dc_run_php_test)

#Run all tests parallel
all-tests-parallel:
	make init-tests
	make cache-clear-test
	$(dc_run_php_test)  --parallel --verbose

test-file: ## Run tests on a single file (ex: make test-file tests/Feature/AuthenticationTest.php)
	$(dc_exec) php vendor/bin/pest $(args) || true

test-filter: ## Run tests by filtering on the case name (ex: make test-filter "authenticates a user")
	$(dc_exec) php vendor/bin/pest --filter "$(args)" || true

group-tests: ## Run tests on groups (ex: make test-group "authentication,accounts")
	$(dc_exec) php vendor/bin/pest --group=$(args) || true

unit-tests: ## Run unit tests
	$(dc_exec) php vendor/bin/pest --testsuite unit

integration-tests: ## Run unit tests
	$(dc_exec) php vendor/bin/pest --testsuite integration

api-tests: ## Run unit tests
	$(dc_exec) php vendor/bin/pest --testsuite api

coverage-report:
	$(dc_exec) php vendor/bin/pest --parallel --coverage-html=var/coverage/ && open var/coverage/index.html

#Run cache clear test
cache-clear-test:
	$(dc_exec) php bin/console cache:clear -e test


##
## Coding standards

lint: phpcbf php-cs-fixer phpcs phpstan ## Run all code linters and fixers

phpcs: ## Run phpcs to correct violations of defined coding guidelines
	$(dc_exec) php vendor/bin/phpcs -s --colors

phpcbf: ## Run phpcbf to automatically fix as many sniff violations as possible
	$(dc_exec) php vendor/bin/phpcbf

phpstan: ## Run phpstan static code analysis
	$(dc_exec) php vendor/bin/phpstan analyse src --memory-limit="-1" --ansi

php-cs-fixer: ## Run php-cs-fixer to fix to follow coding guidelines
	$(dc_exec) php vendor/bin/php-cs-fixer fix

##

help:
	@grep -E '(^[a-zA-Z_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m## /[33m/'
.PHONY: help
.DEFAULT_GOAL := help

%: # This will catch everything to avoid "No rule to make target" errors
	@true
