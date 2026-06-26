# Guide AI-Driven Development

## Démarrage rapide

Ouvre Copilot Chat : `⌘ + Shift + C`

### Créer une entity

```
#file:.github/prompts/create-entity.md

Crée une entity "Favorite" avec productId, userId, createdAt
```

### Créer un composant Vue

```
#file:.github/prompts/create-component.md

Crée un composant "ChannelCard" qui affiche nom et statut du canal
```

### Corriger un bug

```
#file:.github/prompts/fix-bug.md

Bug : le panier ne prend pas en compte le canal actif
Attendu : filtrer les produits par channel
Actuel : retourne tous les produits de tous les channels
```

### Créer une feature complète

```
#file:.github/prompts/create-feature.md

Ticket : MKP-123
Description : Ajouter un filtre par catégorie sur le catalogue produits
```

### Lancer un spike

```
#file:.github/prompts/spike.md

Ticket : MKP-312
Description : Évaluer la migration du système de paiement vers Stripe Connect
```

### Rédiger des spécifications techniques

```
#file:.github/prompts/specs-tech.md

Ticket : MKP-313
Description : Implémenter le système de favoris produits multi-channel
Source : docs/spike/MKP-312-favoris.md
Périmètre : Full Stack
```

### Reviewer une PR en local

```
#file:.github/prompts/review-pr-local.md

Review cette PR.
```

> Compare automatiquement avec `dev`. Pour une autre branche :

```
#file:.github/prompts/review-pr-local.md

Review cette PR par rapport à staging.
```

---

## Structure

```
.github/
├── copilot-instructions.md      ← Lu automatiquement par Copilot
├── git-commit-instructions.md   ← Guide commit pour développeurs
├── agents/                      ← Un agent par domaine
│   ├── symfony-agent.md         # Backend
│   ├── vuejs-agent.md           # Frontend
│   ├── architect-agent.md       # Architecture
│   └── ...
├── skills/                      ← Compétences transverses
│   ├── apply-tdd.md
│   ├── apply-clean-code.md
│   ├── security.md
│   ├── git-conventional-commits.md
│   ├── design-ui.md
│   ├── design-ux.md
│   ├── review-code.md
│   └── refactor-code.md
├── prompts/                     ← Templates prêts à l'emploi
│   ├── create-entity.md
│   ├── create-component.md
│   ├── create-feature.md
│   ├── refactor-method.md
│   ├── review-code.md
│   ├── review-pr-local.md
│   ├── write-tests.md
│   ├── fix-bug.md
│   ├── spike.md
│   └── specs-tech.md
└── ISSUE_TEMPLATE/
    └── ia-review.md             ← Template revue trimestrielle
```

---

## Quand utiliser quoi ?

| Je veux... | J'utilise |
|------------|-----------|
| Créer du code backend | `#file:.github/agents/symfony-agent.md` |
| Créer du code frontend | `#file:.github/agents/vuejs-agent.md` |
| Décider de l'architecture | `#file:.github/agents/architect-agent.md` |
| Écrire en TDD | `#file:.github/skills/apply-tdd.md` |
| Améliorer la lisibilité | `#file:.github/skills/apply-clean-code.md` |
| Sécuriser le code | `#file:.github/skills/security.md` |
| Faire une revue | `#file:.github/prompts/review-code.md` |
| Reviewer une PR en local | `#file:.github/prompts/review-pr-local.md` |
| Refactorer | `#file:.github/prompts/refactor-method.md` |
| Explorer un sujet (spike) | `#file:.github/prompts/spike.md` |
| Rédiger des specs techniques | `#file:.github/prompts/specs-tech.md` |

> 💡 Les agents définissent un **rôle par spécialité**. Le dépôt ne choisit pas le modèle : l'utilisateur le sélectionne directement dans l'IDE.
> Les sujets critiques sont traités par **le même agent**, avec une analyse renforcée et davantage de validations.

---

## Combiner agent + skill

```
#file:.github/agents/symfony-agent.md
#file:.github/skills/apply-tdd.md

Crée un service ChannelAccessService en TDD
```

---

## Workflow feature complète

```
#file:.github/prompts/create-feature.md

Ticket : MKP-123
Feature : Ajouter la gestion des favoris produits par channel
```

Copilot suit automatiquement : Backend → Types → Frontend → Tests

---

## Validation

Après chaque génération :

```bash
make lint                  # Vérifie le code
make all-tests-parallel    # Lance les tests
```

---

## Tips

1. **Sélectionne du code** avant d'ouvrir le chat pour le contexte
2. **Un prompt = une tâche** — reste focalisé
3. **Itère** — demande des ajustements si besoin
4. **Valide toujours** avec `make lint && make all-tests-parallel`
