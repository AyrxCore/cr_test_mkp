# Créer un Composant Vue.js

## Spécifications

- **Nom** : [NOM].vue
- **Module** : [nom du module dans `assets/vuejs/modules/`]
- **Props** : [liste des props avec types TS]
- **Emits** : [événements émis]
- **Fonctionnalité** : [description]

## Instructions

### 1. Composant (`assets/vuejs/modules/[module]/components/[NOM].vue`)

- `<script lang="ts" setup>` — Composition API uniquement
- Props typées avec `defineProps<Props>()` (generics TypeScript)
- Utiliser `defineEmits` pour les événements
- TailwindCSS exclusivement pour le style — pas de `<style>` sauf cas exceptionnel
- Composants partagés depuis `assets/vuejs/modules/shared/`
- Router via `useRouter()` depuis `vue-router`

### 2. Types

- Définir les types dans `assets/vuejs/types/` avec le préfixe `I` (ex: `IMonType`)

### 3. Store (si nécessaire)

- Pinia store dans `assets/vuejs/stores/[domain]Store.ts`
- Interface `[Domain]State` pour le typage de l'état
- Actions async appelant les `ClientService.get()` correspondants
- Flags de chargement (`loaded`, `loading`) et d'erreur

### 4. Nommage

- Composant : `{Domain}{Type}.vue` (ex: `AdherentCard.vue`, `AccordList.vue`)
- Page : `{Domain}Page.vue`
- Store : `{domain}Store.ts`

## Exemple d'utilisation

```
Module : adherents
Crée un composant "AdherentStatusBadge" qui :
- Reçoit en prop un adhérent (type APISchemaApp['Adherent-search'])
- Affiche un badge vert si actif, rouge si inactif
- Émet un événement "toggle" au clic
```

## Validation

```bash
# make lint               # désactivé temporairement
```
