# 01 - Guide de Démarrage Rapide

## 🚀 Mise en route du projet

### Prérequis

Avant de commencer, assurez-vous d'avoir installé :

- **Docker** et **Docker Compose**
- **Make** (disponible par défaut sur macOS/Linux)
- **Node.js 18+** (pour le développement frontend local)
- **PHP 8.3+** (optionnel, pour le linting local)

### Installation initiale

```bash
# 1. Cloner le projet
git clone <repository-url>
cd marketplace

# 2. Créer le fichier d'environnement local
cp .env .env.local

# 3. Configurer les variables d'environnement dans .env.local
# (voir section Configuration ci-dessous)

# 4. Initialiser le projet complet (Docker, BDD, migrations, frontend)
make init
```

### Commandes Docker essentielles

| Commande | Description |
|----------|-------------|
| `make up` | Démarrer tous les containers Docker |
| `make down` | Arrêter et supprimer les containers |
| `make stop` | Arrêter les containers sans les supprimer |
| `make restart` | Redémarrer les containers |
| `make logs php` | Afficher les logs du container PHP |
| `make php-bash` | Accéder au shell du container PHP |
| `make node-bash` | Accéder au shell du container Node.js |

### Commandes de développement

```bash
# Frontend (Vue.js)
make build-front          # Compiler le frontend (production)
npm run dev               # Mode développement avec hot-reload (depuis le container node)

# Backend (Symfony)
make cache-clear          # Vider le cache Symfony
make database-migrations  # Exécuter les migrations
make database-fixtures    # Charger les fixtures de test
make generate-keypair     # Générer les clés JWT

# Qualité de code
make lint                 # Exécuter tous les linters
make phpstan              # Analyse statique PHP

# Tests
make all-tests            # Exécuter tous les tests
make unit-tests           # Tests unitaires uniquement
make api-tests            # Tests API uniquement
```

### Configuration des variables d'environnement

Variables essentielles à configurer dans `.env.local` :

```env
# Application
APP_ENV=dev
APP_DEBUG=1
APP_SECRET=votre_secret_unique

# Base de données PostgreSQL
DATABASE_URL="postgresql://user:password@database:5432/marketplace?serverVersion=15"

# API Djust (connexion à la marketplace)
DJUST_API_BASE_URL=https://djust-api.pre-prod.djust-app.com/qantis
DJUST_API_USERNAME=votre_username
DJUST_API_PASSWORD=votre_password
DJUST_TEST_ACCOUNTS_PASSWORD=votre_test_password

# JWT Authentication
JWT_SECRET_KEY=%kernel.project_dir%/config/jwt/private.pem
JWT_PUBLIC_KEY=%kernel.project_dir%/config/jwt/public.pem
JWT_PASSPHRASE=votre_passphrase

# Emails
MAIL_FROM=noreply@marketplace.local
MAIL_TO=admin@marketplace.local
```

### Accès à l'application

Une fois l'installation terminée :

- **Application** : http://localhost:8080
- **API Documentation** : http://localhost:8080/api/docs

### Structure des containers Docker

| Container | Port | Description |
|-----------|------|-------------|
| nginx | 8080 | Serveur web |
| php-fpm | - | Application PHP/Symfony |
| postgres | 5432 | Base de données |
| js | - | Build du frontend (Vite) |

### Résolution des problèmes courants

**Les migrations échouent ?**
```bash
make database-create
make database-update
make database-migrations
```

**Problème de permissions ?**
```bash
chmod -R 777 var/
```

**Cache corrompu ?**
```bash
make cache-clear
rm -rf var/cache/*
```

**Clés JWT manquantes ?**
```bash
make generate-keypair
```

