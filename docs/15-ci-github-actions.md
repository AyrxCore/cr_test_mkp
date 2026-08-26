# 15 - CI GitHub Actions

## Objectif

Le workflow [`.github/workflows/tests.yml`](../.github/workflows/tests.yml) exécute automatiquement la suite de tests Pest sur chaque Pull Request et sur chaque push vers `main`/`develop`, afin de détecter les régressions avant le merge.

## Déclenchement

```yaml
on:
  pull_request:
  push:
    branches:
      - main
      - develop
```

- **`pull_request`** : se déclenche à l'ouverture, à chaque nouveau commit poussé sur la branche source (`synchronize`), et à la réouverture d'une PR — quelle que soit la branche cible.
- **`push`** : ne se déclenche que sur `main` et `develop`. Un push direct sur une branche de feature sans PR ouverte ne lance rien.

## Déroulé du job

Le job `tests` tourne sur `ubuntu-latest` avec un service **Postgres 17.5** démarré en parallèle, exposé sur `127.0.0.1:5432`.

| Étape | Rôle |
|---|---|
| Checkout | Récupère le code du commit testé |
| Setup PHP | Installe PHP 8.5 et les extensions requises (`pdo_pgsql`, `intl`, `apcu`, etc.) |
| Cache Composer | Restaure le cache des paquets Composer (clé basée sur `composer.lock`) |
| Install dependencies | `composer install --no-interaction --prefer-dist --optimize-autoloader` |
| Setup Node | Installe Node LTS avec cache Yarn intégré |
| Install JS dependencies | `yarn install --frozen-lockfile` |
| Build frontend assets | `yarn build` (génère `public/assets/.vite/manifest.json`, requis par `vite_asset()` dans les templates Twig) |
| Configure test environment | Écrit `DATABASE_URL` dans `.env.test.local` |
| Wait for PostgreSQL | Attend que le service soit prêt (`pg_isready`) |
| Initialize test database | Crée `template_db_test`, applique le schéma Doctrine, le marque `IS_TEMPLATE true` |
| Clear test cache | `cache:clear -e test` |
| Run tests | `php vendor/bin/pest --parallel --verbose` |

## Isolation des bases de test

Chaque test individuel (`setUp`/`tearDown` dans `ApiTestCase` et `IntegrationTestCase`) clone une base de données jetable à partir du template Postgres (`CREATE DATABASE ... WITH TEMPLATE template_db_test`), utilisée puis supprimée à la fin du test. C'est ce qui permet à Pest de faire tourner les tests en parallèle sans collision de données.

Le host de connexion est dérivé dynamiquement de `$_ENV['DATABASE_URL']` (voir [`tests/TestDatabaseCloneTrait.php`](../tests/TestDatabaseCloneTrait.php)) plutôt que codé en dur, pour fonctionner aussi bien en local (réseau docker-compose, `postgres_test_mkp`) qu'en CI (`127.0.0.1`).

## Build frontend requis

Plusieurs templates Twig (`base.html.twig`, `base_app.html.twig`) appellent `vite_asset()`, qui lit `public/assets/.vite/manifest.json`. Ce dossier est gitignoré et généré localement par le conteneur `marketplace-js-1` — en CI il n'existe pas tant que l'étape **Build frontend assets** ne tourne pas. Sans cette étape, tout test qui rend une page (ex: `CartPersistProcessorTest`, `NewsletterRattachementControllerTest`) échoue en 500 avec l'erreur `json_decode(): Argument #1 ($json) must be of type string, false given`.

## Permissions

```yaml
permissions:
  contents: read
```

Le `GITHUB_TOKEN` du job n'a que l'accès lecture au repo (least privilege). `actions/cache` n'a pas besoin de `actions: write` : il s'authentifie via un jeton runtime dédié (`ACTIONS_RUNTIME_TOKEN`), indépendant des permissions déclarées sur le `GITHUB_TOKEN`.

## Debug d'un échec CI

1. Regarder l'étape qui échoue en premier dans les logs Actions.
2. Si l'échec est **spécifique à la CI** (absent en local) : suspecter une étape manquante dans le workflow (build front, extension PHP absente) plutôt qu'un vrai bug de test.
3. Si l'échec est **reproductible en local** (`make all-tests-parallel`) : c'est un vrai problème applicatif, indépendant de la CI — voir [08-tests.md](08-tests.md).

## Voir aussi

- [08 - Tests](08-tests.md) — framework Pest, conventions, exécution locale
- [01 - Guide de démarrage](01-getting-started.md) — setup Docker local
