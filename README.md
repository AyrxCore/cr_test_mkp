# MarketPlace

## Requirements

- PHP >= 8
- Composer - [Install](https://getcomposer.org/doc/00-intro.md#installation-linux-unix-osx)
- Yarn - [Install](https://classic.yarnpkg.com/en/docs/install#debian-stable)
- Node >= 14
- Linux

## Install

```sh
$ make build
```

## Run project

```sh
$ make up
```

Access to http://localhost:8087

# Configuration

Le fichier .env contient tous les paramètres de connexion vers l'api Uppler de prod sauf les secrets
Le fichier .env.dev contient tous les paramètres de connexion vers l'api Uppler de preprod sauf les secrets
Ajouter dans un nouveau fichier .env.local les paramètres de connexion Uppler de preprod (à récuperer sur VAULT)

```sh
UPPLER_ADMIN_CLIENT_ID=
UPPLER_ADMIN_CLIENT_SECRET=
```

## Add a new user

```sh
$ make exec php bin/console user:add {email} {password}
```

## Promote user

```sh
$ make exec php bin/console user:promote {email} {role}
```

## Demote user

```sh
$ make exec php bin/console user:demote {email} {role}
```

## Test unitaires et fonctionnelles

La documentation sur les tests unitaires et fonctionnels se trouve ici : https://qantis.atlassian.net/wiki/spaces/M2/pages/413859841/Test+Unitaires+et+fonctionnels

# Authentification / Login /

## Entités / Base de données

La gestion des utilisateurs et des logins est totalement couplée au fonctionnement de l'api Uppler.

Il existe dans le projet 2 entités User et Account qui stockent les informations de login.

Un User possède autant d'account que de liaison à des company Uppler (Adhérents neo).

Un Account ne stocke pas d'information Uppler si ce n'est des ID de ressources (subAccount, User, Company, ...)

À chaque connexion l'api Uppler est requêtée à l'aide de ces ID pour récupérer les informations à jour.

Ces informations ne sont pas stockées en base, uniquement dans le store VueJS en front.

## Jeu de données de test / Fixtures

Pour fonctionner correctement l'authentification en mode DEV a besoin d'un jeu de données.
Ce jeu de données est délivré par la mécanisme standard sur Symfony des Fixtures.

Les fixtures peuvent être créées via la commande suivante :

`make database-fixtures`

La liste des utilisateurs créés se trouvent de la classe [`App\DataFixtures\Story\UserStory`](src/DataFixtures/Story/UserStory.php)

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

#### Auto Login

Permet l'authentification d'un utilisateur sans passer par le formulaire.
Une requête GET doit être envoyée à /login/auto-login avec les paramètres suivants :

```
  "email": EMAIL DE L'UTILISATEUR,
  "timestamp": TIMESTAMP EN SECONDES,
  "hash": HASH
```

Le hash est généré de la manière suivante :

1. Concatener email, timestamp et la _clé_
2. Hasher le résultat avec SHA256
3. Convertir le hash en base-64

La clé est un Uuid associé à l'adhérent lors de la synchro depuis Neo

En retour est envoyée une url permettant l'authentification automatique de l'utilisateur et la redirection vers la page d'accueil :

```
{
    "url": "URL"
}
```
