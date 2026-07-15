---
version: 1.0
updated: 2026-03
---

# Git Commit Instructions - Qantis MarketPlace

> 👤 **Guide de référence rapide pour développeurs**  
> Pour le skill complet utilisé par les agents AI, voir : `.github/skills/git-conventional-commits.md`

## Format de Commit Obligatoire

```
MKP-XXX: <type>(<scope>): <description in English>
```

### Règles Essentielles

1. **TOUJOURS** commencer par le numéro de ticket : `MKP-XXX:`
2. **Première ligne** < 72 caractères
3. **Impératif présent** en anglais
4. **Commits atomiques** : 1 commit = 1 changement logique

## Types de Commit

| Type       | Usage                                           | Exemple                                                  |
| ---------- | ----------------------------------------------- | -------------------------------------------------------- |
| `feat`     | Nouvelle fonctionnalité                         | `MKP-123: feat(adherent): add status filter`             |
| `fix`      | Correction de bug                               | `MKP-456: fix(tarif): fix discount calculation`          |
| `refactor` | Restructuration sans changement de comportement | `MKP-789: refactor(accord): extract validation logic`    |
| `test`     | Ajout ou modification de tests                  | `MKP-123: test(adherent): add error cases`               |
| `docs`     | Documentation uniquement                        | `MKP-321: docs: update README`                           |
| `style`    | Formatage, pas de changement de code            | `MKP-654: style: apply cs-fixer`                         |
| `perf`     | Amélioration de performance                     | `MKP-987: perf(query): optimize adherent query`          |
| `chore`    | Maintenance, config, CI                         | `MKP-111: chore: update dependencies`                    |

## Scopes Courants

- `adherent` - Gestion des adhérents
- `tarif` - Tarifs et calculs
- `accord` - Accords et conventions
- `offre` - Offres partenaires
- `partner` - Partenaires
- `auth` - Authentification
- `api` - API générale
- `ui` - Interface utilisateur
- `db` - Base de données

## Exemples ✅

```bash
# Feature complète
MKP-1256: feat(auth): add password security rules

# Bugfix
MKP-789: fix(tarif): fix access rights calculation

# Tests
MKP-123: test(adherent): cover error cases

# Refactoring
MKP-456: refactor(accord): extract AccordValidator

# Documentation
MKP-321: docs: add contribution guide
```

## Exemples ❌

```bash
# ❌ Pas de ticket
feat(adherent): add status filter

# ❌ Ticket à la fin
feat(adherent): add status filter MKP-123

# ❌ Trop vague
fix bug
update code
WIP

# ❌ En français
MKP-123: feat(adherent): ajouter le filtre par statut

# ❌ Passé composé / past tense
MKP-123: feat(adherent): added status filter
```

## Nommage des Branches

```
<type>/<ticket>-<description-courte>
```

### Exemples

```bash
feat/MKP-123-adherent-status-filter
fix/MKP-789-discount-calculation
refactor/MKP-654-extract-tarif-service
hotfix/MKP-999-fix-double-payment
```

## Breaking Changes

Pour les changements cassants, ajouter `!` après le scope :

```bash
MKP-999: feat(api)!: rename field `nom` to `lastName`

BREAKING CHANGE: The `nom` field of the /adherents API is renamed to `lastName`.
Clients must update their calls.
```

## Workflow Recommandé

```bash
# 1. Créer la branche
git checkout -b feat/MKP-123-adherent-filter

# 2. Développer par commits atomiques
git commit -m "MKP-123: feat(adherent): add status field"
git commit -m "MKP-123: feat(adherent): expose API filter"
git commit -m "MKP-123: test(adherent): cover status filter"

# 3. Rebaser avant la PR
git rebase -i origin/develop

# 4. Pousser
git push origin feat/MKP-123-adherent-filter
```

## Checklist Avant Commit

- [ ] Format : `MKP-XXX: <type>(<scope>): <description>` ?
- [ ] Ticket au début du message ?
- [ ] Impératif présent en anglais ?
- [ ] Première ligne < 72 caractères ?
- [ ] Commit atomique (1 seul changement logique) ?
- [ ] Tests passent (`make all-tests-parallel`) ?
- [ ] Code formaté (`make lint`) ?

## ⚠️ Pour les agents AI

**JAMAIS exécuter `git commit` automatiquement.** L'IA doit **uniquement proposer** le message de commit ; c'est l'utilisateur qui exécute le commit.

## Référence Complète

Pour plus de détails, voir : `.github/skills/git-conventional-commits.md`
