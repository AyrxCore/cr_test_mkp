# MarketPlace

## 📚 Documentation

| Document                                                           | Description |
|--------------------------------------------------------------------|-------------|
| [01 - Getting Started](docs/01-getting-started.md)                 | Installation et démarrage rapide |
| [02 - Architecture](docs/02-architecture.md)                       | Architecture technique du projet |
| [03 - Entités](docs/03-entities.md)                                | Modèle de données (User, Account, Channel...) |
| [04 - Authentification](docs/04-authentication.md)                 | JWT, OAuth Djust, Auto-login, sécurité |
| [05 - Intégration API Djust](docs/05-djust-integration.md)         | Services Djust, endpoints, authentification |
| [06 - Frontend Vue.js](docs/06-frontend-vuejs.md)                  | Composition API, Pinia, TailwindCSS |
| [07 - Backend Symfony](docs/07-backend-symfony.md)                 | API Platform 4, Services, Providers |
| [08 - Tests](docs/08-tests.md)                                     | Pest PHP, Foundry, stratégie de tests |
| [09 - Channels Multi-tenant](docs/09-channels-multitenant.md)      | Système multi-tenant par Channel |
| [10 - Glossaire & FAQ](docs/10-glossary-faq.md)                    | Termes métier et questions fréquentes |
| [11 - AI-Driven Development](docs/11-ai-driven-development.md)     | 🤖 Guide pratique IA (agents, skills, prompts) |
| [12 - AI Vision & Méthodologie](docs/12-ai-vision-methodologie.md) | 🤖 Vision et méthodologie AI-Driven |
| [13 - Djust Cart Savings Sync](docs/13-djust-cart-savings-sync.md) | Synchronisation des économies panier |
| [14 - Scheduler Pattern](docs/14-scheduler-pattern.md)             | Pattern de planification des tâches |

## 🤖 Agents & Copilot

Ce projet utilise une configuration IA structurée pour guider GitHub Copilot :

```
.github/
├── copilot-instructions.md      ← Instructions globales (lu automatiquement)
├── git-commit-instructions.md   ← Guide commit conventionnel
├── agents/                      ← 3 agents (Symfony, Vue.js, Architecte)
├── skills/                      ← 8 skills (Clean Code, TDD, Sécurité, UI/UX...)
├── prompts/                     ← 9 templates (Feature, Entity, Component, Bug, Tests...)
└── ISSUE_TEMPLATE/              ← Template revue IA trimestrielle
```

> 📖 Voir [`docs/11-ai-driven-development.md`](docs/11-ai-driven-development.md) pour le guide d'utilisation complet.

---

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

> 🔐 **HTTPS en local** — Les certificats SSL ne sont pas dans le repo.  
> Consulter [docs/https-local.md](docs/https-local.md) pour les générer avant le premier `make up`.

## Run project

```sh
$ make up
```

Access to https://localhost:8087

# Configuration

Le fichier .env contient tous les paramètres de connexion vers l'API Djust de preprod sauf les secrets.
Ajouter dans un nouveau fichier .env.local les paramètres de connexion Djust (à récupérer sur VAULT) :

```sh
DJUST_API_BASE_URL=https://djust-api.pre-prod.djust-app.com/qantis
DJUST_API_USERNAME=
DJUST_API_PASSWORD=
DJUST_TEST_ACCOUNTS_PASSWORD=
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

# Authentification / Login

## Entités / Base de données

La gestion des utilisateurs et des logins est totalement couplée au fonctionnement de l'API Djust.

Il existe dans le projet 2 entités `User` et `Account` qui stockent les informations de login.

Un `User` possède autant d'`Account` que de liaisons à des customer accounts Djust (Adhérents Neo).

Un `Account` stocke les identifiants Djust (`djustUsername`, `djustPassword`, `djustCustomerAccountId`, `djustCustomerUserId`).

À chaque connexion, l'API Djust est requêtée pour récupérer les informations à jour et obtenir un access token.

Ces informations ne sont pas stockées en base, uniquement dans le store VueJS en front.

## Jeu de données de test / Fixtures

Pour fonctionner correctement l'authentification en mode DEV a besoin d'un jeu de données.
Ce jeu de données est délivré par la mécanisme standard sur Symfony des Fixtures.

Les fixtures peuvent être créées via la commande suivante :

`make database-fixtures`

La liste des utilisateurs créés se trouvent de la classe [`App\DataFixtures\Story\UserStory`](src/DataFixtures/Story/UserStory.php)

https://symfony.com/bundles/DoctrineFixturesBundle/current/index.html

## Authentification Front/Back

L'authentification entre le front et le back repose sur l'obtention d'un **JWT token** passé dans un **cookie HttpOnly**.
La particularité de ce cookie est qu'il ne peut pas être lu par JavaScript, uniquement par le back (PHP).
Une fois le cookie obtenu, il doit être joint à toutes les requêtes vers l'API afin de s'authentifier.

**Toutes les requêtes d'authentification sont interceptées par `App\Security\UserChecker`**

Documentation : https://symfony.com/doc/current/security/user_checkers.html

Les règles implémentées dans ce UserChecker sont :

- Pour se connecter, un `User` doit être actif (`isEnabled = true`), sinon la connexion est refusée
- Pour se connecter, un `User` doit avoir au moins un `Account` lié, sinon la connexion est refusée

- Si un user possède un seul account, sa session est hydratée automatiquement avec son `access_token` et son `Account`
- Si un user ne possède aucun account, la connexion est refusée
- **Exception** : Les plateformes externes (Neo) peuvent dialoguer avec l'API sans `Account` si elles possèdent le rôle `ROLE_API`

**⚠️ Attention** : En cas de redémarrage du container Docker, il faut détruire l'ancien cookie du navigateur (il ne sera plus reconnu par la nouvelle session).

## Authentification Back / Djust

### Mécanisme / Architecture

L'authentification entre le back et l'API Djust repose sur **OAuth2** : obtention d'un `access_token` en envoyant `username` / `password`.
Une fois le token obtenu, il doit être passé dans chaque requête dans le header `Authorization: Bearer {token}`.
Ce mécanisme est géré de manière transparente dans `DjustHttpClientService`.

### Implémentation dans les services Symfony

Tous les services devant communiquer avec Djust utilisent `DjustHttpClientService` qui gère automatiquement :
- La récupération et le renouvellement du token
- La gestion du cache (tokens opérateur)
- La gestion de session (tokens)
- Le retry automatique en cas de 401

**2 types de token sont gérés :**

#### Token Operator (Opérateur)

Un token **opérateur** permet d'effectuer des requêtes en mode admin sur l'API Djust.
Ce token est obtenu avec les credentials configurés dans `.env.local` :

```bash
DJUST_API_USERNAME=   # Username opérateur
DJUST_API_PASSWORD=   # Password opérateur
```

Le token est **mis en cache** (Symfony Cache) et partagé entre toutes les sessions.
- Durée de vie : **240 secondes** (4 minutes)
- Cache key : `djust_operator_token`
- Renouvellement automatique en cas d'expiration

#### Token ACCOUNT (Utilisateur)

Un token **account** permet d'effectuer des requêtes scopées aux permissions de l'`Account` connecté.
Ce token est obtenu avec les credentials stockés dans l'entité `Account` (`djustUsername` / `djustPassword`).

- Durée de vie : **240 secondes** (4 minutes)
- Stockage : **session PHP** (`djust_account_access_token`, `djust_account_refresh_token`, `djust_account_expires_at`)
- L'`Account` est synchronisé depuis Neo
- Renouvellement automatique en cas d'expiration

### Logs API

Tous les appels vers l'API Djust sont loggés dans un fichier dédié : `/var/log/djust.log`
Un canal Monolog spécifique a été créé : `djust`.

```yaml
# config/packages/monolog.yaml
djust:
    type: stream
    path: "%kernel.logs_dir%/djust.log"
    level: info
    channels: ["djust"]
```

Documentation : https://symfony.com/doc/current/logging/channels_handlers.html

## Auto Login

Permet l'authentification d'un utilisateur **sans passer par le formulaire** (utilisé par Neo).
Une requête `GET` doit être envoyée à `/login/auto-login` avec les paramètres suivants :

```json
{
  "email": "user@example.com",
  "timestamp": 1234567890,
  "hash": "BASE64_HASH"
}
```

**Génération du hash :**

1. Concaténer : `email` + `timestamp` + `clé secrète`
2. Hasher le résultat avec **SHA256**
3. Convertir le hash en **base64**

La clé secrète est un UUID associé à l'adhérent lors de la synchronisation depuis Neo.

**Réponse** : URL permettant l'authentification automatique et la redirection vers l'app :

```json
{
  "url": "https://marketplace.qantis.co/auto-login?token=..."
}
```

> 📖 Voir [`docs/04-authentication.md`](docs/04-authentication.md) pour plus de détails sur l'authentification.
