# Review de PR en Local

## Contexte

- **Branche à reviewer** : `$CURRENT_BRANCH` (branche courante)
- **Branche de comparaison** : `dev` (par défaut — préciser si différente)
- **Ticket** : MKP-XXX
- **Type de changement** : [feature | fix | refactor | chore]

## Instructions

> **Agent** : `architect-agent` + skill `review-code`
> **Règle** : tout le feedback est donné **directement dans le chat** — ne jamais créer de fichier de review.

### 1. Récupérer le diff

Exécuter automatiquement :

```bash
git --no-pager diff dev..HEAD --stat
git --no-pager diff dev..HEAD
```

> ⚠️ **Toujours utiliser `--no-pager`** pour éviter que Git ouvre `less` en mode interactif dans le terminal de l'agent.
> Si le terminal se bloque sur un affichage paginé, taper `:q` pour quitter `less` et relancer avec `--no-pager`.

> Si l'utilisateur précise une autre branche de comparaison, remplacer `dev` par celle indiquée.
> Sinon, **toujours comparer avec `dev`** sans demander.

### 2. Analyse du diff

Pour chaque fichier modifié, appliquer la grille de review complète :

#### Sécurité (🔴 bloquant)

- [ ] Pas de secret/credential en dur
- [ ] `security` sur les opérations API Platform sensibles
- [ ] Groups de sérialisation explicites
- [ ] Validation des entrées backend (Assert, DTO)
- [ ] Pas de données sensibles en `localStorage`
- [ ] Pas de SQL brut non paramétré

#### Architecture & Patterns

- [ ] Logique métier dans Services, pas Controllers/Processors
- [ ] Provider/Processor pour les cas custom API Platform
- [ ] Nommage conforme aux conventions du projet
- [ ] `declare(strict_types=1);` sur tous les fichiers PHP
- [ ] Constructor property promotion avec `readonly`

#### Qualité du code

- [ ] Pas de code mort ou commenté
- [ ] Méthodes courtes (< 30 lignes)
- [ ] Noms explicites
- [ ] Pas de duplication
- [ ] Early return, pas de if/else imbriqués
- [ ] Gestion d'erreurs appropriée

#### Frontend (si applicable)

- [ ] `<script lang="ts" setup>` — Composition API
- [ ] Props typées avec generics + types API
- [ ] TailwindCSS uniquement
- [ ] Gestion d'erreurs API non silencieuse

#### Tests

- [ ] Tests présents pour le nouveau code
- [ ] Tests existants non cassés
- [ ] Cas nominaux + cas d'erreur couverts

### 3. Format de sortie (dans le chat uniquement)

Le rendu doit **toujours** suivre cette structure exacte :

```markdown
## 📊 Résumé du diff `<branche>` vs `dev`

**Fichiers modifiés** : X | **Ajouts** : +Y | **Suppressions** : -Z

## Résumé

La PR apporte :
- Point principal 1
- Point principal 2
- Bonus : ... (si applicable)

---

## ✅ Ce qui est bien

**`Fichier.php`** — Titre court du point positif.
Description détaillée de pourquoi c'est bien, ce que ça apporte.

**`AutreFichier.vue`** — Autre point positif.
Description.

---

## ⚠️ Points à discuter / améliorer

### 1. `Fichier.php` — Titre du problème

```lang
// Extrait de code concerné (avant/après si pertinent)
```

Explication du problème, du risque, et de la recommandation.

### 2. `AutreFichier.php` — Titre du problème

```lang
// Extrait de code
```

Explication et suggestion de correction.

---

## 🔴 Bloquant

| # | Fichier | Problème |
|---|---------|----------|
| 1 | `fichier.sh` | Description courte du bloquant |

## 🟡 À corriger

| # | Fichier | Problème |
|---|---------|----------|
| 2 | `Fichier.php` | Description courte |
| 3 | `Autre.php` | Description courte |

## 🟢 Globalement

Synthèse en 1-2 phrases. Verdict clair : peut merger après correction du/des bloquant(s), ou prêt à merger. 👍

> `make lint` → `make all-tests-parallel`
```

### 4. Règles

1. **Ne jamais créer de fichier** — tout le feedback va dans le chat
2. **Toujours comparer avec `dev`** sauf indication contraire explicite
3. **Commencer par un résumé narratif** de ce que la PR apporte
4. **✅ Ce qui est bien en premier** — toujours souligner les points positifs avant les problèmes
5. **⚠️ Points à améliorer avec extraits de code** — montrer le code concerné, expliquer le problème, proposer la correction
6. **Tableaux récap par sévérité** (🔴 Bloquant → 🟡 À corriger) — vue synthétique rapide
7. **🟢 Verdict global** en fin — une phrase claire sur l'état de la PR
8. Terminer par le rappel `make lint` → `make all-tests-parallel`
9. Proposer un message de commit conventionnel si la branche n'en a pas
10. Si aucun bloquant : omettre le tableau 🔴 et indiquer directement 🟢

## Exemple d'utilisation

```
Je suis sur la branche feature/MKP-456-add-tarif-export.
Review cette PR.
```

→ L'agent fait `git diff dev..HEAD`, analyse, et donne son feedback dans le chat.

```
Review la branche feature/MKP-789 par rapport à staging.
```

→ L'agent fait `git diff staging..feature/MKP-789` au lieu de `dev`.
