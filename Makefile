php-bash:
	docker exec -it marketplace-php-fpm-1 bash

node-bash:
	docker exec -it marketplace-nodejs-1 sh

nginx-bash:
	docker exec -it marketplace-nginx-1 sh

db-bash:
	docker exec -it marketplace-db-1 sh

lint:
	php vendor/bin/php-cs-fixer fix
	php vendor/bin/phpstan analyse
	yarn eslint

build:
	git pull origin master
	cp .env.prod .env
	docker exec marketplace-php-fpm-1 composer install
	docker exec marketplace-php-fpm-1 php bin/console doctrine:migrations:migrate
	docker  exec marketplace-php-fpm-1 php bin/console lexik:jwt:generate-keypair
	docker exec marketplace-nodejs-1  yarn install
	docker exec marketplace-nodejs-1  yarn build

load-fixtures:
	$(EXEC_PHP) bin/console d:f:l -q --group=dev
