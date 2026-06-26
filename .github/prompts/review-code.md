# Revoir du Code

## Contexte

- **Fichier(s)** : [fichier(s) à reviewer]
- **Type de changement** : [feature, fix, refactor]
- **Points d'attention** : [sécurité, performance, architecture...]

## Instructions — Grille de Review

### 1. Sécurité (bloquant)

- [ ] Pas de secret/credential en dur ou commité
- [ ] `security` sur les opérations API Platform sensibles (`is_granted('ROLE_...')`)
- [ ] Groups de sérialisation explicites — pas d'exposition de données sensibles par défaut
- [ ] Validation des entrées côté backend (Assert, DTO) — pas de confiance au front
- [ ] Pas de données sensibles en `localStorage` côté front
- [ ] Pas de SQL brut non paramétré (injection)

### 2. Architecture & Patterns

- [ ] Logique métier dans les Services, pas dans les Controllers/Processors
- [ ] Provider/Processor pour les cas custom API Platform
- [ ] Message/Handler pour les traitements async
- [ ] Nommage conforme aux conventions du projet
- [ ] `declare(strict_types=1);` sur tous les fichiers PHP
- [ ] Constructor property promotion avec `readonly`

### 3. Qualité du code

- [ ] Pas de code mort ou commenté
- [ ] Méthodes courtes (< 30 lignes idéalement)
- [ ] Noms explicites (variables, méthodes, classes)
- [ ] Pas de duplication
- [ ] Early return plutôt que if/else imbriqués
- [ ] Gestion d'erreurs (exceptions, try/catch côté front)

### 4. Frontend spécifique

- [ ] `<script lang="ts" setup>` — Composition API
- [ ] Props typées avec `defineProps<Props>()` (generics) + types API (`APISchemaApp`)
- [ ] TailwindCSS uniquement — pas de CSS custom
- [ ] Gestion d'erreurs API non silencieuse (notifications)
- [ ] Composants partagés réutilisés (`NeoButton`, `NeoModal`, etc.)

### 5. Tests

- [ ] Tests présents pour le nouveau code
- [ ] Tests existants non cassés
- [ ] Factories Foundry utilisées pour les données de test
- [ ] Cas nominaux + cas d'erreur couverts

### 6. Format de sortie

Pour chaque point, indiquer :

- **OK** : conforme
- **WARNING** : suggestion d'amélioration (non bloquant)
- **CRITICAL** : problème bloquant à corriger

## Exemple d'utilisation

```
Fichier(s) : src/State/Processor/TarifProcessor.php, src/Service/TarifService.php
Type de changement : feature (ajout transfert de tarifs)
Points d'attention : sécurité (vérifier les droits), performance (nombre d'adhérents potentiellement élevé)
```
