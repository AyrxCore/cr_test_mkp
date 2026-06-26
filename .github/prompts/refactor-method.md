# Refactorer du Code

## Contexte

- **Fichier(s)** : [fichier(s) à refactorer]
- **Problème** : [code smell identifié ou raison du refactoring]

## Instructions

### 1. Analyse

- Identifier le code smell : méthode trop longue, responsabilité multiple, duplication, couplage fort, nommage flou
- Vérifier les tests existants couvrant le code à refactorer (`make all-tests-parallel`)
- Lister les usages/dépendances du code (appelants, héritiers)

### 2. Refactoring — Principes

- **Un changement à la fois** — ne pas mélanger refactoring et feature
- **Pas de changement de comportement** — les tests existants doivent continuer à passer
- Appliquer les patterns du projet :
  - **Backend** : Service pour la logique métier, Provider/Processor pour API Platform, Message/Handler pour l'async
  - **Frontend** : Composition API, composables pour la logique réutilisable, stores Pinia pour l'état
- Constructor property promotion avec `readonly`
- `declare(strict_types=1);`
- Nommage conforme aux conventions du projet

### 3. Techniques courantes

| Smell                      | Technique                                    |
| -------------------------- | -------------------------------------------- |
| Méthode trop longue        | Extract Method                               |
| Classe trop large          | Extract Class / Service                      |
| Duplication                | Extract vers Trait (PHP) ou composable (Vue) |
| If/else imbriqués          | Early return, Strategy pattern, Enum         |
| Logique dans Controller    | Déplacer vers Service                        |
| Logique dans Processor     | Déplacer vers Service dédié                  |
| Composant Vue monolithique | Extraire sous-composants                     |

### 4. Validation

- Les tests existants passent sans modification → le comportement est préservé
- Ajouter des tests si la couverture est insuffisante
- Commit : `MKP-XXX: refactor(<scope>): <description in English>`

## Exemple d'utilisation

```
Fichier : src/State/Processor/TarifProcessor.php
Problème : La méthode process() fait 120 lignes avec de la validation, de la logique métier et des notifications. Extraire la logique métier dans un TarifService.
```

## Validation

```bash
make lint
make all-tests-parallel
```
