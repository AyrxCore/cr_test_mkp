# MarketPlace

## Requirements

- PHP >= 8
- Composer - [Install](https://getcomposer.org/doc/00-intro.md#installation-linux-unix-osx)
- Yarn - [Install](https://classic.yarnpkg.com/en/docs/install#debian-stable)
- Node >= 14
- Linux

## Install

```sh
$ docker-compose build
```

## Run project

```sh
$ docker-compose up
```

Access to http://localhost:8087

# Configuration
dupliquer le fichier .env en .env.local
le fichier .env contient tous les paramétres de connexion vers l'api Uppler de prod
Dupliquer ces paramètres dans le .env.local et les ajuster pour l'api de preprod

```sh
UPPLER_ENV=dev
UPPLER_API_URL="https://api.preprod-yousg3q-qbpekzlwwankw.fr-3.platformsh.site/"
UPPLER_ADMIN_CLIENT_ID="9_5fsndcuwidk4kc8wgcw8c0gww8k8444448sccs4ssc8scsgc00"
UPPLER_ADMIN_CLIENT_SECRET="2ikh2lc57y4g8o80coo04sc8c0ckogkkos8o840cw84k0sw88c"
```


## Add a new user

```sh
$ php bin/console user:add {email} {password}
```

## Promote user

```sh
$ php bin/console user:promote {email} {role}
```

## Demote user

```sh
$ php bin/console user:demote {email} {role}
```

## Test unitaires et fonctionnelles
Afin de lancer les tests
 `sh runtest.sh`

# Authentification / Login / 

## Entités / Base de données

La gestion des utilisateurs et des logins est totalement couplée au fonctionnement de l'api Uppler.

Il existe dans le projet 2 entités User et Account qui stockent les informations de login.

Un User posséde autant d'account que de liaison à des company Uppler (Adhérents neo).

Un Account ne stocke pas d'information Uppler si ce n'est des ID de ressources (subAccount, User, Company, ...)

A chaque connexion l'api Uppler est requêtée à l'aide de ces ID pour récupérer les informations à jour.

Ces informations ne sont pas stockées en base, uniquement dans le store VueJS en front.

## Jeu de données de test / Fixtures

Pour fonctionner correctement l'authentification en mode DEV a besoin d'un jeu de test.
Ce jeu de test est délivré par la mécanisme standard sur Symfony des Fixtures.
Le jeu de fixtures est disponible dans le fichier app/DataFixtures/UserFixtures.
Les fixtures doivent être injectées automatiquement au docker_compose build grâce à une ligne dans le docker_compose.yaml (d:f:l --group=dev -q)
Si toutefois les fixtures ne sont pas injectées il suffit de lancer cette en cli.
```
services:
  php-fpm:
    build:
      context: .
      target: php
    environment:
      - APP_ENV=${APP_ENV}
      - APP_SECRET=${APP_SECRET}
    command: sh -c "composer i -o ; wait-for-it db:5432 -- bin/console doctrine:migrations:migrate -n; bin/console d:f:l --group=dev -q;  bin/console lexik:jwt:generate-keypair --overwrite -n; php-fpm"
    volumes:
      - ./:/var/www
      - ./docker/php-fpm/xdebug.ini:/usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
      - ./docker/php-fpm/error_reporting.ini:/usr/local/etc/php/conf.d/error_reporting.ini
    extra_hosts:
      - 'host.docker.internal:host-gateway'
    networks:
      - dev-marketplace
```
2 users sont créés : 

- mfrebet@qatis.co / 000000 => attaché à un seul compte Acheteur et actif
- buyer@qantis.oc / 000000 => attaché à 2 comptes acheteurs et inactif donc nécessité de passer par première connexion

https://symfony.com/bundles/DoctrineFixturesBundle/current/index.html

## Authentification Front/Back

L'authentification entre le front et la back repose sur l'obtention d'un Token passé dans un cookie HttpOnly.
La particularité de ce cookie est qu'il ne peut pas être lu par javascript, uniquement par le back donc PHP.
Une fois le cookie obtenu il doit être joint à toutes les requêtes vers l'api afin de s'authentifier.

**Toutes les requêtes d'authentification sont interceptées et analysées par un userChecker, App/Security/UserChecker.php**

https://symfony.com/doc/5.4/security/user_checkers.html

Les règles implémentées dans ce userChecker sont les suivantes :
- Pour pouvoir se connecter un user doit être actif (isEnabled) sinon userChecker refuse la connexion
- Pour pouvoir se connecter un user doit avoir au moins un account lié sinon userChecker refuse la connexion

- Si un user vient de se connecter et qu'il ne possède qu'un seul account, on hydrate sa session avec son access_token et son account car à réception de cette information le front va l'envoyer directement dans l'app
- Si un user vient de se connecter et qu'il ne possède aucun account (une exception existe cf : ci-dessous), la connexion est refusée.
- Une exception est toutefois consentie à la règle ci-dessus afin que des plateformes externes (neo par exemple) puisse dialoguer avec l'api sans avoir d'account. Il est pour cela nécessaire de posséder le rôle 'ROLE_API'


**Attention !!! En cas redémarrage du container Docker il faut penser à détruire l'ancien cookie du navigateur car il ne sera plus reconnu par la nouvelle session.**

## Authentification Back / Uppler

### Mécanisme / Architecture 

L'authentification entre le back et l'api Uppler repose sur Oauth2 et donc dans ce cas 
l'obtention d'un accessToken en envoyant un couple client_id/client_secret.
Une fois le token obtenu il doit être passé dans chaque requête dans une en-tête Authorization BEARER.
Ce mécanisme est géré de manière totalement transparente dans la classe de connexion abstraite HttpClientProvider.

### implémentation dans les services Symfony

Tous les services devant communiquer avec Uppler devraient étendre cette classe afin de s'affranchir de la gestion du token.

2 types de token peuvent être générés.

#### Token Admin

Un token admin permet d'effectuer sur l'api UPPLER des requêtes en mode Admin 'Operator'.
Ce token est obtenu grace à un couple client_id/client_secret issue du back office et stockés en dur 
dans le fichier conf/services.yaml qui s'hydrate grace aux memes valeurs situées dans les fichiers .env.
```
parameters:
  uppler_env: '%env(UPPLER_ENV)%'
  uppler_api_url: '%env(UPPLER_API_URL)%'
  uppler_admin_client_id: '%env(UPPLER_ADMIN_CLIENT_ID)%'
  uppler_admin_client_secret: '%env(UPPLER_ADMIN_CLIENT_SECRET)%'
```
Si un token Admin est récupéré il est stocké sur le serveur dans le fichier /var/token.txt ainsi il est disponbile et partagé entre toues les sessions.
Ce token a une durée de vie de 3600 secondes, au dela de laquelle il doit être remplacé.
La classe HttpClientProvider gére cela de manière transparente, toutes les requêtes sont centralisées et les code retour sont analysés
Si un code 401 est retourné alors le token est renégocié, remplacé dans le fichier token.txt et la requete envoyée à nouveau.

#### Token Utilisateur (buyer)

Un token utilisateur permet d'effectuer des appels sur l'api UPPLER en mode utilisateur (Buyer)
Le back Office est ainsi scopé aux seules permissions du user authentifié.
Ce token est obtenu grace à un couple client_id/client_secret stocké en base dans l'entité Account à connecter.
Il a une durée de vie de 3600 secondes, au dela de laquelle il doit être remplacé. 
Il est stockée en session (sous le nom access_token) ainsi que l'account de manière a être naturellement attaché à l'utilisateur connecté.
L'entité Account a elle même été créé et hydratée par la synchronization issue de neo.
La classe HttpClientProvider gére cela de manière transparente, toutes les requêtes sont centralisées et les code retour sont analysés
Si un code 401 est retourné alors le token est renégocié, remplacé dans la session (access_token) et la requete envoyée à nouveau.

### Logs api

Tous les appels vers l'api Uppler, essentiellement les erreurs, sont stockées dans un fichier /var/log/api.log
Pour exploiter ce fichier un canal monolog spécifique a ét créé le canal 'api'.

```
    api:
        type: stream
        path: "%kernel.logs_dir%/api.log"
        level: error
        channels: [ "api" ]
```
https://symfony.com/doc/5.4/logging/channels_handlers.html
