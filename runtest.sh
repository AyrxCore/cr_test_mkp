#!/usr/bin/env bash

# Suppression de la base de données

php bin/console doctrine:database:drop --force --env=test

# Création de la base de données

php bin/console doctrine:database:create --env=test

php bin/console --env=test doctrine:schema:create



php bin/phpunit
