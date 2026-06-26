# Spécifications Techniques

## Contexte

- **Ticket** : MKP-XXX
- **Description** : [description du besoin à spécifier]
- **Source** : [ticket Jira | spike `docs/spike/MKP-XXX-*.md` | description libre]
- **Périmètre** : [Backend | Frontend | Full Stack]

## Instructions

### 1. Analyse de l'entrée

- Lire le ticket ou le spike référencé
- Extraire les exigences fonctionnelles
- Identifier le domaine impacté (entities, services, composants, routes)
- Lister les contraintes connues (sécurité, performance, compatibilité)

### 2. Conception technique

Pour chaque couche impactée, détailler :

**Backend (si concerné)** :
- Entities à créer/modifier (propriétés, relations, contraintes)
- Endpoints API (méthode, URL, sérialisation, sécurité)
- Services / Providers / Processors nécessaires
- Messages async si applicable
- Migrations BDD

**Frontend (si concerné)** :
- Composants à créer/modifier
- Stores Pinia impactés
- Routes à ajouter
- Types TypeScript nécessaires

### 3. Sécurité et validation

- Règles d'accès par endpoint (roles, voters)
- Contraintes de validation (Assert)
- Groupes de sérialisation (données exposées)

### 4. Stratégie de tests

- Tests à écrire par type (Unit, API, Feature, Integration)
- Cas nominaux et cas d'erreur principaux

### 5. Livrable

Produire un fichier Markdown synthétique dans `docs/specs-tech/` :

- Nom : `MKP-XXX-<slug>.md`
- Structure imposée (voir template ci-dessous)
- Ton direct et technique, pas de prose
- Peu d'emojis (aucun dans le corps, 1-2 max en titres si besoin)
- Diagrammes en Mermaid si un flux le justifie

### Template de sortie

```markdown
# Specs MKP-XXX : <Titre>

**Date** : YYYY-MM-DD
**Auteur** : <nom ou "AI-assisted">
**Statut** : Draft | Validé | Implémenté
**Source** : <lien ticket ou spike>

## Résumé

<1-3 phrases décrivant ce qui doit être implémenté>

## Modèle de données

| Entity    | Propriété   | Type     | Contraintes          | Notes        |
| --------- | ----------- | -------- | -------------------- | ------------ |
| ...       | ...         | ...      | ...                  | ...          |

### Relations

- `EntityA` OneToMany `EntityB` (...)

## Endpoints API

| Méthode | URL                     | Rôle requis  | Input         | Output         |
| ------- | ----------------------- | ------------ | ------------- | -------------- |
| GET     | /api/...                | ROLE_...     | -             | Collection     |
| POST    | /api/...                | ROLE_...     | DTO           | Item           |

## Services et logique métier

| Classe                | Responsabilité              |
| --------------------- | --------------------------- |
| `XxxService`          | ...                         |
| `XxxProcessor`        | ...                         |

## Frontend

| Composant / Store     | Rôle                        |
| --------------------- | --------------------------- |
| `XxxPage.vue`         | ...                         |
| `xxxStore.ts`         | ...                         |

## Sécurité

- Voters : ...
- Sérialisation : groupes `read` / `write` sur ...
- Validation : ...

## Plan de tests

| Type        | Cible                  | Cas couverts                  |
| ----------- | ---------------------- | ----------------------------- |
| Unit        | `XxxService`           | ...                           |
| API         | `POST /api/...`        | 201, 401, 403, 422            |
| Feature     | Flux complet ...       | ...                           |

## Découpage en tâches

1. [ ] ...
2. [ ] ...
3. [ ] ...

## Notes / Questions ouvertes

- ...
```

## Exemple d'utilisation

```
Ticket : MKP-456
Description : Implémenter la migration du système de notifications vers Symfony Notifier
Source : spike docs/spike/MKP-312-migration-notifications.md
Périmètre : Full Stack
```

## Conventions

- Respecter le nommage déjà utilisé dans le projet et dans le ticket/spike de référence
- Noms de classes, propriétés et endpoints concrets (pas de placeholders génériques)
- Chaque spec doit être implémentable sans ambiguïté
- Commit : `MKP-XXX: docs(specs): <description in English>`
