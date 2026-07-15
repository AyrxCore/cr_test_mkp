---
version: '1.0'
updated: 2026-03
next-review: 2026-06
---

# Copilot Instructions - Qantis MarketPlace

## Sommaire

- [Processus obligatoire](#-processus-obligatoire)
- [Projet](#projet)
- [Commandes](#commandes-obligatoires)
- [Conventions de code](#conventions-de-code)
- [Routage agents/skills](#routage-automatique--agents--skills)

---

## 🚨 Processus Obligatoire

**Checklist obligatoire AVANT toute génération de code :**

| Étape | Action                                                                          | Obligatoire |
| ----- | ------------------------------------------------------------------------------- | ----------- |
| 1     | Lire ce fichier                                                                 | ✅ Toujours  |
| 2     | Déterminer le domaine : `Symfony` \| `Vue.js` \| `Architecture` \| `Full Stack` | ✅ Toujours  |
| 3     | Évaluer la criticité et le périmètre pour adapter la profondeur d'analyse        | ✅ Toujours  |
| 4     | Résoudre l'agent (voir 🔑 ci-dessous)                                          | ⚡ Conditionnel |
| 5     | Résoudre les skills (voir 🔑 ci-dessous)                                       | ⚡ Conditionnel |
| 6     | **Annoncer** agent et skills (format ci-dessous)                                | ✅ Toujours  |
| 7     | Coder selon les conventions                                                     | ✅ Toujours  |
| 8     | **Rappeler** à l'utilisateur : `make lint` → `make all-tests-parallel`          | ✅ Toujours  |
| 9     | Proposer commit conventionnel (**jamais exécuter** `git commit` automatiquement) | ✅ Toujours  |

> **🔑 Étapes 4 & 5 — Résolution agent & skills :**
> - **L'utilisateur précise** un agent ou des skills dans son prompt → **les utiliser directement**, passer aux étapes 6+.
> - **L'utilisateur ne précise rien** → les déterminer automatiquement via le [Routage Automatique](#routage-automatique--agents--skills).
> - Si un fichier agent ou skill référencé n'existe pas dans `.github/agents/` ou `.github/skills/`, **signaler l'absence** et continuer avec les conventions de ce fichier uniquement.

> **⚡ Fast path — demande triviale** (typo, renommage, question rapide) :
> Les étapes 2-6 peuvent être condensées en une seule ligne d'annonce. Ne pas surcharger la réponse de cérémonie.

### ⚠️ À FAIRE ABSOLUMENT À CHAQUE NOUVELLE SECTION DE TRAVAIL (nouveau sujet / feature)

1. Faire un plan détaillé
2. Créer une checklist
3. Cocher chaque étape une fois réalisée

**Format d'annonce obligatoire :**

```
📋 Agent : {agent-name}
🛠️ Skills : apply-clean-code, security, git-conventional-commits [+ contextuels]
```

---

## Projet

Application marketplace B2B : catalogue produits, commandes et gestion multi-tenant (Channels) via API Djust.

- **Backend** : PHP 8.3, Symfony 6.4, API Platform 4, Doctrine ORM, PostgreSQL
- **Frontend** : Vue.js 3, TypeScript, Pinia, TailwindCSS, Vite
- **Tests** : Pest PHP, Foundry, Mockery
- **Auth** : JWT (LexikJWTAuthenticationBundle), cookies HttpOnly
- **Intégration** : API Djust (OAuth2, tokens Operator)
- **Multi-tenant** : Channels (chaque `Channel` possède son design et ses paramètres propres, et regroupe plusieurs `Adherent`; un `Adherent` appartient à un seul `Channel`)

> Détails : `docs/02-architecture.md` · `.github/agents/` · `.github/skills/`

---

## Commandes Obligatoires

```bash
make up                    # Démarrer les conteneurs Docker
make lint                  # PHP-CS-Fixer + PHPStan + PHPCS + PHPCBF
make unit-tests            # Tests unitaires uniquement (rapide)
make all-tests-parallel    # Tous les tests (API, Unit, Feature, Integration)
make database-migrations   # Exécuter les migrations Doctrine
make database-diff         # Générer une migration depuis les changements d'entités
make build-front           # Build du frontend (yarn install + yarn build)
```

---

## Conventions de Code

### PHP/Symfony

- `declare(strict_types=1);` en début de fichier
- Constructor property promotion avec `readonly` (services uniquement, pas sur les entities)
- Attributs PHP 8 — pas de commentaires — PSR-12
- API Platform 4 avec attributs `#[ApiResource]`, State Providers/Processors

### Vue/TypeScript

- Composition API `<script setup lang="ts">` — TailwindCSS uniquement — Pinia
- Composants dans `assets/vuejs/modules/` organisés par domaine
- Stores dans `assets/vuejs/stores/`

### Sécurité & qualité (essentiels)

| Règle                      | ✅ Bon                                         | ❌ Mauvais                          |
| -------------------------- | ---------------------------------------------- | ----------------------------------- |
| Sérialisation API Platform | `normalizationContext: ['groups' => ['read']]` | Exposer toute l'entité              |
| Validation entrées         | DTO avec constraints Symfony                   | Validation manuelle dans controller |
| Autorisations              | Voter + attribut `security`                    | Vérification côté front uniquement  |
| Secrets                    | Variables d'env + `.env.local`                 | Hardcoder ou logger des secrets     |
| Erreurs front              | `catch` + notification utilisateur             | `catch` silencieux                  |
| Stockage tokens            | Cookies HttpOnly pour JWT                      | Tokens en localStorage              |
| API Djust                  | `DjustHttpClientService` (gestion token auto)  | Appels directs sans gestion token   |

### Nommage

| Type                 | Convention                                   | Exemple                  |
| -------------------- | -------------------------------------------- | ------------------------ |
| Service              | `{Domain}Service`                            | `ChannelService`         |
| Repository           | `{Entity}Repository`                         | `AccountRepository`      |
| Message              | `{Action}{Entity}Message` / `{Action}{Entity}MessageHandler` | `SyncAdherentMessage` |
| Provider / Processor | `{Entity}Provider` / `{Entity}Processor`     | `ChannelProvider`        |
| Composant Vue        | `{Domain}{Type}.vue`                         | `ChannelCard.vue`        |
| Store Pinia          | `{domain}Store.ts`                           | `channelStore.ts`        |
| Interface TS         | `I{Name}`                                    | `IChannel`               |
| Composable           | `use{Name}.ts`                               | `useDebounce.ts`         |
| API Service          | `{domain}Api.ts`                             | `channelApi.ts`          |
| Enum TS              | `E{Name}`                                    | `EAccordStatut`          |
| DTO                  | `{Entity}{Action}Input/Output`               | `AccountCreateInput`     |

### Commit

Format : `MKP-XXX: <type>(<scope>): <description in English>` — détail dans `skills/git-conventional-commits.md`

---

## Routage Automatique — Agents & Skills

Sélectionner **automatiquement** l'agent et les skills **uniquement si l'utilisateur ne les a pas précisés dans son prompt**.

Le dépôt **ne route jamais vers un modèle**. L'agent et les skills guident la réponse, mais **le choix du modèle se fait par l'utilisateur directement dans l'IDE**.

> Note : ce routage/ces "skills" sont une **convention interne du repo** (dossier `.github/`) pour guider la génération, pas une capacité Copilot standard.

Si l'utilisateur mentionne explicitement un agent ou des skills → **les respecter sans re-déterminer**.
Sinon, ne jamais demander à l'utilisateur.

> Exception : si une information manque et bloque l'implémentation, poser **1 seule question** courte ; sinon faire 1-2 hypothèses et les annoncer.

### 1. Domaine

| La demande concerne...                                                 | Domaine                                  |
| ---------------------------------------------------------------------- | ---------------------------------------- |
| Entity, Repository, Service, API, Doctrine, Controller, Migration, PHP, Djust | **Symfony**                      |
| Composant, Store, Vue, Pinia, TailwindCSS, TypeScript front            | **Vue.js**                               |
| Architecture, patterns, choix techniques, ADR                          | **Architecture**                         |
| Backend + Frontend                                                     | **Full Stack** → architecte puis délègue |

### 2. Criticité & profondeur d'analyse

| Situation | Attendu |
| --------- | ------- |
| CRUD, endpoint, composant, bug, logique métier, optimisation, workflow | Analyse standard et ciblée |
| Sécurité paiement, haute charge, bug critique prod, refonte archi, intégration Djust critique | Analyse approfondie, checklist renforcée, validation des risques |

### 3. Agent (`.github/agents/`)

| Domaine          | Agent unique         |
| ---------------- | -------------------- |
| **Symfony**      | `symfony-agent`      |
| **Vue.js**       | `vuejs-agent`        |
| **Architecture** | `architect-agent`    |

### 4. Skills (`.github/skills/`)

**Toujours chargés** : `apply-clean-code` · `security` · `git-conventional-commits`

| Contexte                                | Skill additionnel |
| --------------------------------------- | ----------------- |
| Tests, TDD, couverture                  | `apply-tdd`       |
| Composant, design, UI                   | `design-ui`       |
| Parcours utilisateur, UX, accessibilité | `design-ux`       |
| Review, PR, qualité                     | `review-code`     |
| Refactoring, code smell                 | `refactor-code`   |

### Règles

1. Un seul agent par spécialité : la criticité change la profondeur d'analyse, pas l'agent
2. Full Stack → `architect-agent` orchestre puis délègue à `symfony-agent` et/ou `vuejs-agent`
3. Max 2 skills supplémentaires recommandé ; 3 accepté si justifié
4. En cas de doute → choisir l'agent du domaine principal et renforcer la revue
