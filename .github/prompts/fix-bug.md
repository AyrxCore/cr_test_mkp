# Corriger un Bug

## Description

- **Ticket** : MKP-XXX
- **Comportement attendu** : [ce qui devrait se passer]
- **Comportement actuel** : [ce qui se passe]
- **Étapes pour reproduire** : [comment reproduire]
- **Message d'erreur** : [erreur si applicable]

## Instructions

### 1. Diagnostic

- Identifier le fichier/la couche concernée (Entity, Service, Provider, Processor, Composant, Store)
- Chercher dans les logs (`docker/logs/`) si erreur serveur
- Vérifier les contraintes de validation et la sérialisation (`#[Groups]`)
- Vérifier les autorisations (`security`, Voters)

### 2. Correction

- Appliquer le fix minimal et ciblé — ne pas refactorer au passage
- `declare(strict_types=1);` si absent du fichier modifié
- Respecter les patterns existants (Provider/Processor, Service, etc.)

### 3. Test de non-régression

- Écrire un test Pest qui reproduit le bug **avant** le fix (rouge)
- Appliquer le fix → le test passe (vert)
- Structure : `tests/[API|Unit|Feature|Integration]/` selon la couche
- Utiliser les Foundry factories pour les données de test
- Pattern Arrange / Act / Assert avec `expect()`

### 4. Commit

- Format : `MKP-XXX: fix(<scope>): <description in English>`

## Exemple d'utilisation

```
Ticket : MKP-789
Comportement attendu : GET /api/accords/{id} retourne nbAdherents = 3 quand 3 adhérents actifs
Comportement actuel : nbAdherents retourne 5 (compte aussi les inactifs)
Étapes : Créer un accord avec 3 adhérents actifs et 2 inactifs, GET l'accord
Message d'erreur : aucun, valeur incorrecte
```

## Validation

```bash
# make lint               # désactivé temporairement
make all-tests-parallel
```
