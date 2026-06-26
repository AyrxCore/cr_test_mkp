# Créer une Feature Full Stack

## Spécifications

- **Ticket** : MKP-XXX
- **Titre** : [titre de la feature]
- **Description** : [description détaillée incluant objectifs et critères d'acceptation]

## Instructions — Workflow séquentiel

### Étape 1 : Backend (Symfony)

1. **Entity** (si nouvelle) dans `src/Entity/` — `#[ApiResource]`, `#[Groups]`, `#[Assert\...]`, `security`
2. **Repository** dans `src/Repository/` si requêtes custom nécessaires
3. **Service** dans `src/Service/` pour la logique métier — nommage `{Domain}Service`
4. **State Provider/Processor** dans `src/State/` si logique de lecture/écriture personnalisée
5. **Message + Handler** dans `src/Message/` + `src/MessageHandler/` si traitement async
6. **Migration** : exécuter `make database-diff`
7. **Factory** dans `src/Factory/` pour les tests

### Étape 2 : Types TypeScript

- Vérifier que les nouveaux types sont définis dans `assets/vuejs/types/`

### Étape 3 : Frontend (Vue.js)

1. **Service API** dans `assets/vuejs/services/` si nouvel endpoint
2. **Store Pinia** dans `assets/vuejs/stores/` — interface `State`, actions async, flags loading/error
3. **Composants** dans `assets/vuejs/modules/[module]/components/` — `<script lang="ts" setup>`, TailwindCSS
4. **Page** dans `assets/vuejs/modules/[module]/views/` si nouvelle page
5. **Route** dans `assets/vuejs/router.ts` si nouvelle page

### Étape 4 : Tests

1. **Tests API** dans `tests/Api/` — Pest, Foundry factories, `createClientWithCredentials()`
2. **Tests unitaires** dans `tests/Unit/` pour les services
3. **Tests feature** dans `tests/Feature/` pour les flux complets

## Conventions

- Nommage : voir la documentation du domaine concerné dans `docs/` (par exemple `docs/06-frontend-vuejs.md` pour le front)
- Commit : `MKP-XXX: feat(<scope>): <description in English>`
- Ne pas exposer de données sensibles — groups de sérialisation explicites
- Validation côté backend (pas de confiance au front)
- Gestion d'erreurs API non silencieuse côté front

## Exemple d'utilisation

```
Ticket : MKP-456
Titre : Ajouter la gestion des notes sur les adhérents
Description :
- Un adhérent peut avoir plusieurs notes (texte libre + date + auteur)
- CRUD complet via API
- Liste des notes affichée sur la page détail adhérent
- Seuls les ROLE_ADMIN peuvent supprimer une note
```

## Validation

```bash
make lint
make all-tests-parallel
make database-diff         # si modification d'entité
make database-migrations   # si nouvelle migration
```
