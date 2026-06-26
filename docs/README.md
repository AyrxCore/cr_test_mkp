# 📚 Documentation MarketPlace

> **MarketPlace** est une application de marketplace B2B développée avec **Symfony 6.4** (backend) et **Vue.js 3** (frontend), intégrée avec l'API Uppler.

---

## 📋 Sommaire

| # | Document | Description |
|---|----------|-------------|
| 01 | [Guide de démarrage](01-getting-started.md) | Installation, configuration, commandes essentielles |
| 02 | [Architecture](02-architecture.md) | Vue d'ensemble, structure des dossiers, flux de données |
| 03 | [Entités](03-entities.md) | Modèle de données, relations entre entités |
| 04 | [Authentification](04-authentication.md) | JWT, OAuth Uppler, Auto-login, sécurité |
| 05 | [Intégration Uppler](05-uppler-integration.md) | Services Uppler, endpoints API, mocking |
| 06 | [Frontend Vue.js](06-frontend-vuejs.md) | Stores Pinia, composants, services HTTP |
| 07 | [Backend Symfony](07-backend-symfony.md) | API Platform, controllers, services |
| 08 | [Tests](08-tests.md) | Pest PHP, tests unitaires/API, mocks |
| 09 | [Multi-tenant](09-channels-multitenant.md) | Channels, personnalisation, feature flags |
| 10 | [Glossaire & FAQ](10-glossary-faq.md) | Définitions, questions fréquentes |

---

## 🚀 Démarrage rapide

```bash
# Cloner et initialiser le projet
git clone <repository-url>
cd marketplace
cp .env .env.local
# Configurer les variables dans .env.local
make init
```

👉 Voir [01-getting-started.md](01-getting-started.md) pour les détails complets.

---

## 🛠️ Stack Technique

### Backend
| Technologie | Version | Usage |
|-------------|---------|-------|
| PHP | 8.3+ | Langage |
| Symfony | 6.4 | Framework |
| API Platform | 4.x | API REST |
| Doctrine ORM | 3.x | Base de données |
| Lexik JWT | 2.x | Authentification |

### Frontend
| Technologie | Usage |
|-------------|-------|
| Vue.js 3 | Framework (Composition API) |
| TypeScript | Typage statique |
| Pinia | State management |
| Tailwind CSS | Styling |
| Vite | Bundler |

### Infrastructure
| Technologie | Usage |
|-------------|-------|
| Docker | Containerisation |
| PostgreSQL | Base de données |
| Nginx | Serveur web |

---

## 🔑 Points clés à comprendre

### 1. Architecture multi-tenant (Channels)
L'application supporte plusieurs **marques/clients** via le système de channels. Chaque channel a son propre design et ses propres adhérents.

👉 Voir [09-channels-multitenant.md](09-channels-multitenant.md)

### 2. Double authentification
- **JWT** pour l'API interne Symfony
- **OAuth2** pour communiquer avec l'API Uppler

👉 Voir [04-authentication.md](04-authentication.md)

### 3. Intégration API Uppler
Toutes les données métier (produits, panier, commandes) proviennent de l'API Uppler via des services dédiés.

👉 Voir [05-uppler-integration.md](05-uppler-integration.md)

### 4. Frontend SPA
Application Vue.js 3 single-page avec routing côté client et state management Pinia.

👉 Voir [06-frontend-vuejs.md](06-frontend-vuejs.md)

---

## 📁 Structure du projet

```
marketplace/
├── assets/vuejs/       # Frontend Vue.js
├── config/             # Configuration Symfony
├── docs/               # 📍 Cette documentation
├── migrations/         # Migrations Doctrine
├── public/             # Assets publics
├── src/                # Backend Symfony
├── templates/          # Templates Twig (pré-home)
├── tests/              # Tests Pest PHP
├── docker-compose.yml  # Configuration Docker
└── Makefile            # Commandes make
```

---

## ⚡ Commandes les plus utilisées

```bash
make up                 # Démarrer Docker
make down               # Arrêter Docker
make php-bash           # Shell PHP
make all-tests          # Lancer les tests
make lint               # Vérifier le code
make database-migrations # Migrations BDD
```

---

## 📞 Besoin d'aide ?

1. Consulter le [Glossaire & FAQ](10-glossary-faq.md)
2. Vérifier les fichiers de documentation spécifiques
3. Contacter l'équipe technique

