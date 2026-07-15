# 02 - Architecture du Projet

## 🏗️ Vue d'ensemble

MarketPlace est une application **B2B** construite sur une architecture **API-First** avec :
- **Backend** : Symfony 6.4 + API Platform 4
- **Frontend** : Vue.js 3 (SPA) avec TypeScript
- **Base de données** : PostgreSQL
- **Infrastructure** : Docker

```
┌─────────────────────────────────────────────────────────────┐
│                        FRONTEND                              │
│                   Vue.js 3 + TypeScript                      │
│              (Composition API, Pinia, Vue Router)            │
└─────────────────────────────────────────────────────────────┘
                              │
                              ▼ API REST (JWT Auth)
┌─────────────────────────────────────────────────────────────┐
│                        BACKEND                               │
│                 Symfony 6.4 + API Platform                   │
│                                                              │
│  ┌─────────────┐  ┌─────────────┐  ┌─────────────────────┐  │
│  │ Controllers │  │  Services   │  │   Djust Services    │  │
│  └─────────────┘  └─────────────┘  └─────────────────────┘  │
│                              │                   │           │
│                              ▼                   ▼           │
│                     ┌─────────────┐     ┌─────────────────┐  │
│                     │  Doctrine   │     │   API Djust     │  │
│                     │    ORM      │     │   (Externe)     │  │
│                     └─────────────┘     └─────────────────┘  │
└─────────────────────────────────────────────────────────────┘
                              │
                              ▼
                    ┌─────────────────┐
                    │   PostgreSQL    │
                    └─────────────────┘
```

## 📁 Structure des dossiers

### Backend (Symfony)

```
src/
├── Command/              # Commandes console Symfony
├── Constants/            # Constantes de l'application
├── Context/              # Contexte métier (Channel context...)
├── Controller/
│   ├── Account/          # Gestion des comptes utilisateurs
│   ├── Api/              # Endpoints API Platform (Buyer, CMS...)
│   └── Custom/           # Contrôleurs personnalisés
├── DataFixtures/         # Fixtures pour les tests
├── Doctrine/             # Extensions Doctrine
├── Dto/                  # Data Transfer Objects
├── Entity/               # Entités Doctrine (User, Account, Channel...)
├── Enum/                 # Énumérations PHP 8.1+
├── EventListener/        # Listeners d'événements
├── EventSubscriber/      # Subscribers d'événements
├── Events/               # Classes d'événements custom
├── Exception/            # Exceptions personnalisées
├── Factory/              # Factories pour création d'objets
├── Form/                 # Formulaires Symfony
├── Helper/               # Classes utilitaires
├── Message/              # Messages pour Symfony Messenger
├── MessageHandler/       # Handlers des messages asynchrones
├── Repository/           # Repositories Doctrine
├── Security/
│   ├── Authentication/   # Handlers d'authentification
│   └── Voter/            # Voters pour les autorisations
├── Serializer/           # Normaliseurs/Dénormaliseurs custom
├── Service/              # Services métier (Djust, etc.)
├── State/                # State Providers/Processors API Platform
├── Twig/                 # Extensions Twig
└── Utils/                # Utilitaires divers
```

### Frontend (Vue.js)

```
assets/
├── main.ts               # Point d'entrée de l'application
├── style/                # Styles globaux (Tailwind, CSS)
└── vuejs/
    ├── App.vue           # Composant racine
    ├── BaseTemplate.vue  # Template de base avec layout
    ├── constants/        # Constantes frontend
    ├── directives/       # Directives Vue personnalisées
    ├── modules/          # Modules par fonctionnalité
    │   ├── account/      # Gestion du compte utilisateur
    │   ├── actualites/   # Pages actualités/news
    │   ├── cart/         # Panier et checkout
    │   ├── contact/      # Formulaire de contact
    │   ├── home/         # Page d'accueil
    │   ├── login/        # Authentification
    │   ├── map/          # Carte des partenaires
    │   ├── products/     # Catalogue produits
    │   └── shared/       # Composants réutilisables
    ├── router/           # Configuration des routes
    ├── services/         # Services API et utilitaires
    │   └── httpclient/   # Clients HTTP (Axios)
    ├── stores/           # Stores Pinia (state management)
    └── types/            # Interfaces TypeScript
```

### Configuration

```
config/
├── packages/             # Configuration des bundles Symfony
│   ├── api_platform.yaml
│   ├── doctrine.yaml
│   ├── security.yaml
│   └── ...
├── routes/               # Définition des routes
├── services/             # Services YAML additionnels
├── jwt/                  # Clés JWT (private.pem, public.pem)
├── channels.yaml         # Configuration des canaux (multi-tenant)
├── channel_option_keys.yaml  # Options disponibles par canal
├── services.yaml         # Services principaux
└── services_test.yaml    # Services pour les tests
```

## 🔄 Flux de données typique

### Exemple : Affichage d'un produit

```
1. [Vue.js] ProductPage.vue
      │
      ▼ Appel store Pinia
2. [Pinia] useProductStore.getProduct(id)
      │
      ▼ HTTP Request (Axios)
3. [HttpClient] ProductHttpClient.getProduct(id)
      │
      ▼ GET /api/buyer/product/{id}
4. [API Platform] State Provider
      │
      ▼
5. [Service] DjustProductService.findProductById()
      │
      ▼ HTTP vers API Djust
6. [Djust API] Données produit
      │
      ▼ Réponse JSON
7. [Vue.js] Affichage du produit
```

## 🔀 Multi-tenant (Channels)

L'application supporte plusieurs **canaux** (tenants) :

- Chaque canal a sa propre apparence (logo, couleurs)
- Les channels sont identifiés par leur hostname
- La configuration est dans `config/channels.yaml`

```
Channel (QANTIS_ACHAT)
    │
    ├── Adherents (entreprises adhérentes)
    │       │
    │       └── Accounts (comptes utilisateurs liés)
    │               │
    │               └── User (utilisateur)
    │
    └── ChannelParameter (design, options)
```

