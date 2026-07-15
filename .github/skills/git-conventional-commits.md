# Skill: Écrire des Commits Conventionnels

> 🤖 **Ce fichier est un skill pour les agents AI**  
> Pour la référence rapide développeurs, voir : `.github/git-commit-instructions.md`

## Identité

Tu es un expert en gestion de version Git. Tu structures l'historique de commits pour qu'il soit lisible, exploitable et automatisable (changelogs, releases).

## Format Conventional Commits

### Structure

```
MKP-XXX: <type>(<scope>): <description in English>

[body]

[footer]
```

> ⚠️ Le numéro de ticket `MKP-XXX:` est **obligatoire** au début de chaque commit.

### Types Autorisés

| Type | Usage | Exemple |
|------|-------|---------|
| `feat` | Nouvelle fonctionnalité | `MKP-123: feat(adherent): add status filter` |
| `fix` | Correction de bug | `MKP-456: fix(tarif): fix discount calculation` |
| `refactor` | Restructuration sans changement de comportement | `MKP-789: refactor(accord): extract validation logic` |
| `test` | Ajout ou modification de tests | `MKP-123: test(adherent): add error cases` |
| `docs` | Documentation uniquement | `MKP-321: docs: update README` |
| `style` | Formatage, pas de changement de code | `MKP-654: style: apply cs-fixer` |
| `perf` | Amélioration de performance | `MKP-987: perf(query): optimize adherent query` |
| `chore` | Maintenance, config, CI | `MKP-111: chore: update dependencies` |
| `ci` | Changements CI/CD | `MKP-222: ci: add phpstan step` |
| `build` | Changements de build | `MKP-333: build: update vite.config` |

### Scope (optionnel mais recommandé)

Le scope correspond au domaine métier ou au module touché :

```
feat(adherent): ...
fix(tarif): ...
refactor(offre): ...
test(accord): ...
feat(auth): ...
fix(payment): ...
```

### Breaking Changes

```
MKP-999: feat(api)!: rename field `nom` to `lastName`

BREAKING CHANGE: The `nom` field of the /adherents API is renamed to `lastName`.
Clients must update their calls.
```

## Exemples Bons / Mauvais

### ❌ Mauvais

```bash
# Trop vague
git commit -m "fix bug"
git commit -m "update code"
git commit -m "changes"
git commit -m "WIP"

# Pas de type
git commit -m "add filter"
git commit -m "fix calculation"

# Pas de ticket MKP-XXX au début
git commit -m "feat(adherent): add status filter"

# Ticket à la fin au lieu du début
git commit -m "feat(adherent): add status filter MKP-123"

# Trop long (> 72 caractères en première ligne)
git commit -m "MKP-123: feat(adherent): add active and inactive status filter with pagination"

# Mélange plusieurs changements
git commit -m "MKP-123: feat: add filter + fix calculation + refactor service"

# En français alors que les commits doivent être en anglais
git commit -m "MKP-123: feat(adherent): ajouter le filtre par statut"
```

### ✅ Bon

```bash
# Clair et atomique avec ticket en premier
git commit -m "MKP-123: feat(adherent): add status filter"
git commit -m "MKP-456: fix(tarif): fix volume discount calculation"
git commit -m "MKP-789: refactor(accord): extract AccordValidator from service"
git commit -m "MKP-123: test(adherent): cover invalid creation cases"
git commit -m "MKP-321: docs: document payment API"

# Avec body pour les changements complexes
git commit -m "MKP-456: feat(tarif): implement progressive calculation

Calculation now applies a degressive rate based on volume:
- 0-50 units: full rate
- 51-200 units: -10%
- 200+ units: -20%"

# Breaking change
git commit -m "MKP-999: feat(api)!: migrate to API Platform 4

BREAKING CHANGE: Custom filters now use PHP 8 attributes.
See migration guide in docs/migration-api-platform.md"
```

## Règles de Nommage des Branches

### Format

```
<type>/<ticket>-<description-courte>
```

### Exemples

```bash
# Features
feat/MKP-123-adherent-status-filter
feat/MKP-456-progressive-tarif-calculation

# Bugfixes
fix/MKP-789-discount-calculation
fix/MKP-321-date-display

# Refactoring
refactor/MKP-654-extract-tarif-service

# Hotfix (production urgente)
hotfix/MKP-999-fix-double-payment
```

### ❌ Mauvais noms de branches

```bash
# Trop vague
feature/update
fix/bug
dev/test

# Pas de ticket
feat/adherent-filter

# Trop long
feat/MKP-123-add-active-and-inactive-status-filter-with-pagination-and-sort-by-creation-date
```

## Commits Atomiques

### Principe

Un commit = **UN** changement logique. Si on peut décrire le commit avec "et", il faut le découper.

### ❌ Mauvais - Commit monolithique

```bash
git commit -m "MKP-123: feat: add Partenaire entity with API, store and component"
# Contient : Entity + Migration + Provider + Store + Composant + Tests
```

### ✅ Bon - Commits atomiques

```bash
git commit -m "MKP-123: feat(partenaire): create entity and migration"
git commit -m "MKP-123: feat(partenaire): expose API with Provider"
git commit -m "MKP-123: test(partenaire): add API tests"
git commit -m "MKP-123: feat(partenaire): create Pinia store"
git commit -m "MKP-123: feat(partenaire): create PartenaireList component"
git commit -m "MKP-123: test(partenaire): add component tests"
```

### Taille Idéale

| Trop petit | ✅ Bon | Trop gros |
|------------|--------|-----------|
| `MKP-1: style: add a space` | `MKP-123: feat(adherent): add filter` | `MKP-123: feat: implement entire module` |
| `MKP-1: fix: typo` | `MKP-456: fix(tarif): fix calculation` | `MKP-456: fix: fix all sprint bugs` |
| `MKP-1: refactor: rename variable` | `MKP-789: refactor(accord): extract AccordValidator` | `MKP-789: refactor: restructure entire backend` |

## Messages de Commit Exploitables

### Structure du Message

```
Ligne 1 : MKP-XXX: <type>(<scope>): <description>  (< 72 caractères)
           ↓ ligne vide
Ligne 3+: body (optionnel, détails, contexte, pourquoi)
           ↓ ligne vide
Footer  : BREAKING CHANGE (si applicable)
```

### Verbes à l'Impératif Présent (en anglais)

```bash
# ✅ Impératif (comme si on donnait un ordre au code)
feat(adherent): add status filter
fix(tarif): fix discount calculation
refactor(accord): extract validation logic

# ❌ Passé composé ou descriptif
feat(adherent): added status filter
fix(tarif): the discount calculation was fixed
refactor(accord): extraction of validation
```

## Workflow Git Recommandé

```
1. Créer la branche
   └── git checkout -b feat/MKP-123-adherent-filter

2. Développer par petits commits atomiques
   ├── git commit -m "MKP-123: feat(adherent): add status field"
   ├── git commit -m "MKP-123: feat(adherent): expose API filter"
   └── git commit -m "MKP-123: test(adherent): cover status filter"

3. Rebaser avant la PR
   └── git rebase -i origin/develop  (squash si nécessaire)

4. Créer la Pull Request
   └── Titre : MKP-123: feat(adherent): add status filter

5. Après review, merge
   └── Squash merge si commits de fixup
```

## Checklist Commits

Avant chaque commit :

- [ ] Le type est-il correct (feat, fix, refactor, test...) ?
- [ ] Le scope est-il précisé (adherent, tarif, accord...) ?
- [ ] La description est-elle à l'impératif présent en anglais ?
- [ ] La première ligne fait-elle < 72 caractères ?
- [ ] Le commit est-il atomique (UN seul changement logique) ?
- [ ] Un body est-il nécessaire pour expliquer le contexte ?
- [ ] Les références de tickets sont-elles incluses (MKP-XXX) ?
- [ ] Les breaking changes sont-ils signalés avec `!` et `BREAKING CHANGE:` ?

Avant chaque push :

- [ ] L'historique est-il propre (pas de "WIP", "fix typo" en série) ?
- [ ] Les commits racontent-ils une histoire cohérente ?
- [ ] La branche est-elle à jour avec develop (`git rebase`) ?

## Instructions

1. **TOUJOURS** utiliser le format `MKP-XXX: <type>(<scope>): <description in English>`
2. **TOUJOURS** commencer par le numéro de ticket `MKP-XXX:`
3. **TOUJOURS** écrire à l'impératif présent en anglais
4. **TOUJOURS** garder la première ligne < 72 caractères
5. **TOUJOURS** faire des commits atomiques (un changement = un commit)
6. **TOUJOURS** inclure le scope quand c'est pertinent
7. **JAMAIS** de commits vagues ("fix", "update", "WIP", "changes")
8. **JAMAIS** de commits sans numéro de ticket
9. **JAMAIS** mélanger plusieurs types de changements dans un commit
10. Signaler les breaking changes avec `!` et footer `BREAKING CHANGE:`
11. **JAMAIS exécuter `git commit` automatiquement** — proposer uniquement le message de commit à l'utilisateur
