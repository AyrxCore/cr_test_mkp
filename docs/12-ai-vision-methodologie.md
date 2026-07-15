# AI-Driven Development - Vision & Méthodologie Qantis

## 1. Vision & Objectifs

L'intégration de l'IA transforme le cycle de développement logiciel chez Qantis. Cette initiative vise à améliorer la productivité, la qualité du code et la satisfaction de l'équipe en réduisant les tâches répétitives.

L'approche est hybride et s'adapte à la complexité de la tâche via 3 niveaux d'interaction distincts :

### Mode Chat (Assistant)

- **Usage** : Assistance conversationnelle en temps réel.
- **Cas type** : Générer une fonction unitaire, écrire une DocBlock, expliquer une erreur, créer un test unitaire isolé.
- **Rôle de l'IA** : Copilote passif (attend vos questions).

### Mode Agent (IDE / @workspace)

- **Usage** : Assistance contextuelle avancée dans VSCode/PhpStorm.
- **Cas type** : Refactoring d'une classe en tenant compte de tout le projet, modification de plusieurs fichiers simultanés, compréhension de l'architecture globale.
- **Rôle de l'IA** : Développeur Junior guidé (exécute des tâches complexes sous vos yeux).

### Copilot Coding Agent (Autonome)

- **Usage** : Délégation asynchrone complète.
- **Cas type** : "Prends l'issue #42 et propose-moi une Pull Request". L'agent planifie, code, teste et soumet le travail de manière autonome.
- **Rôle de l'IA** : Membre de l'équipe autonome (vous ne voyez que le résultat final à reviewer).

> Dans ce nouveau paradigme, le rôle du développeur évolue : il passe de **rédacteur de code** à **Architecte et Reviewer**. L'exécution technique est déléguée à l'IA, tandis que la conception, la validation de la logique métier et la garantie de la qualité restent sous la responsabilité exclusive de l'humain.

---

## 2. Concepts clés (Lexique)

| Terme | Définition | Exemple |
|-------|------------|---------|
| **Prompt** | La commande ou la question envoyée à l'IA pour déclencher une action. | "Génère un Controller pour la gestion des commandes." |
| **Instruction** | La directive précise qui guide la manière dont l'IA doit répondre. | "Utilise uniquement l'injection de dépendance par constructeur et PHP 8.3." |
| **Contexte** | L'ensemble des informations fournies à l'IA pour qu'elle comprenne l'environnement du projet. | Fournir l'accès à `Channel.php` pour que l'IA identifie les relations. |
| **Agent** | Une instance d'IA configurée avec un rôle spécifique et des outils pour accomplir une mission complexe. | "Agent Symfony" configuré pour respecter les standards Qantis. |
| **Skill** | Une compétence technique explicite attribuée à un Agent. | "TDD" ou "Clean Code". |
| **Tool** | Une capacité d'action concrète donnée à l'IA pour interagir avec l'environnement. | Lire un fichier, exécuter une commande terminal. |
| **Feedback loop** | Le cycle d'itération où le développeur corrige ou affine la proposition de l'IA. | Demander un refactoring après une première génération. |

---

## 3. Ressources & Configuration (GitHub)

### Structure du dépôt

```
.github/
├── copilot-instructions.md      ← Instructions globales (lu automatiquement)
├── git-commit-instructions.md   ← Guide commit pour développeurs
├── agents/                      ← Un agent par domaine
│   ├── symfony-agent.md         # Backend PHP/Symfony
│   ├── vuejs-agent.md           # Frontend Vue.js/TypeScript
│   ├── architect-agent.md       # Décisions d'architecture
│   └── ...
├── skills/                      ← Compétences transverses
│   ├── apply-tdd.md             # Test-Driven Development
│   ├── apply-clean-code.md      # Code propre et lisible
│   ├── security.md              # Sécurité applicative OWASP
│   ├── git-conventional-commits.md # Commits conventionnels
│   ├── design-ui.md             # Design system TailwindCSS
│   ├── design-ux.md             # Expérience utilisateur
│   ├── review-code.md           # Revue de code
│   └── refactor-code.md         # Techniques de refactoring
├── prompts/                     ← Templates prêts à l'emploi
│   ├── create-entity.md
│   ├── create-component.md
│   ├── create-feature.md
│   ├── refactor-method.md
│   ├── review-code.md
│   ├── write-tests.md
│   ├── fix-bug.md
│   ├── spike.md
│   └── specs-tech.md
└── ISSUE_TEMPLATE/
    └── ia-review.md             # Template revue trimestrielle
```

### Agents disponibles

> Les agents définissent un **rôle par spécialité**. Le dépôt ne choisit pas le modèle : l'utilisateur le sélectionne directement dans l'IDE.
> Les sujets critiques sont traités par **le même agent**, avec une analyse renforcée, des checklists supplémentaires et une revue plus stricte.

#### Agents uniques par spécialité

| Agent | Fichier | Rôle |
|-------|---------|------|
| **Symfony** | `.github/agents/symfony-agent.md` | CRUD, endpoints, logique métier, intégration Djust, sécurité et cas critiques backend |
| **Vue.js** | `.github/agents/vuejs-agent.md` | Composants, formulaires, stores, style, performance et cas critiques frontend |
| **Architecte** | `.github/agents/architect-agent.md` | Validation de patterns, arbitrages, ADR, migrations et décisions structurantes |

> 💡 **Règle** : conserver un seul agent par spécialité. La criticité augmente la profondeur d'analyse, pas le nombre d'agents.

### Skills disponibles

| Skill | Fichier | Expertise |
|-------|---------|-----------|
| Appliquer le TDD | `.github/skills/apply-tdd.md` | Cycle RED-GREEN-REFACTOR |
| Appliquer le Clean Code | `.github/skills/apply-clean-code.md` | Code lisible, SOLID |
| Sécuriser l'application | `.github/skills/security.md` | OWASP, validation, auth |
| Commits conventionnels | `.github/skills/git-conventional-commits.md` | Format MKP-XXX |
| Designer l'UI | `.github/skills/design-ui.md` | Composants TailwindCSS |
| Designer l'UX | `.github/skills/design-ux.md` | Feedback utilisateur |
| Revoir du Code | `.github/skills/review-code.md` | Revue qualité/sécurité |
| Refactorer du Code | `.github/skills/refactor-code.md` | Techniques de refactoring |

> 📖 **Pour en savoir plus sur l'utilisation des agents, skills et prompts** : consulter le guide complet [`docs/11-ai-driven-development.md`](./11-ai-driven-development.md)

---

## 4. Le Workflow "Issue-to-Code"

1. **Création de l'Issue GitHub** : Rédiger une issue détaillée incluant le contexte, les données d'entrée (Input), le résultat attendu (Output) et les contraintes techniques.

2. **Chargement du Contexte** : Dans l'IDE (VSCode ou PhpStorm), charger le contexte global du projet (`@workspace`).

3. **Prompting & Délégation** :
   - Pour une tâche simple : Utiliser le **Mode Chat** pour générer le code.
   - Pour une tâche complexe (Multi-fichiers) : Utiliser le **Mode Agent IDE** (`@workspace`).
   - Pour une délégation complète : Activer le **Copilot Coding Agent**.

4. **Review Humaine** : Analyser le code produit. Le développeur reste le garant final de la sécurité, de la performance et de la conformité.

---

## 5. Gouvernance & Maintenance

### Versioning des configurations

Chaque fichier agent et instruction contient un header YAML :

```yaml
---
version: 1.0
updated: 2026-03
next-review: 2026-06
---
```

### Revue trimestrielle

Une revue est planifiée tous les 3 mois pour synchroniser les agents avec l'évolution du projet.

**Template disponible** : `.github/ISSUE_TEMPLATE/ia-review.md`

Pour créer une revue : GitHub → New Issue → "🔄 Revue IA Trimestrielle"

### Checklist de revue

- [ ] Vérifier cohérence `copilot-instructions.md` avec la stack actuelle
- [ ] Mettre à jour les versions dans les agents (PHP, Symfony, Vue, API Platform, etc.)
- [ ] Supprimer les skills obsolètes
- [ ] Intégrer les nouveaux patterns adoptés par l'équipe
- [ ] Collecter les retours d'expérience

---

## 6. Les 5 Règles d'Or (Golden Rules)

| # | Règle | Explication |
|---|-------|-------------|
| 1 | **Ne jamais commiter sans relire** | L'IA peut halluciner, proposer du code obsolète ou introduire des failles logiques. La relecture est obligatoire. |
| 2 | **Protection des données sensibles** | Ne jamais soumettre de mots de passe, clés API Djust, tokens JWT ou données clients dans les prompts. |
| 3 | **Primauté du contexte** | Une erreur de l'IA provient souvent d'un contexte insuffisant. Fournir les bons fichiers et règles métier en amont. |
| 4 | **Refactoring immédiat** | Si l'IA génère du code dupliqué ou complexe, demander immédiatement une simplification. |
| 5 | **Esprit critique** | Toujours challenger la solution proposée. Si elle semble trop complexe, demander une approche plus simple. |

---

## 7. Pour en savoir plus

### Documentation interne

- **Guide pratique** : [`docs/11-ai-driven-development.md`](./11-ai-driven-development.md) — Exemples concrets d'utilisation des agents et prompts.
- **Architecture** : [`docs/02-architecture.md`](./02-architecture.md) — Architecture technique du projet.

