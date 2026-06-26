# Spike — Exploration et Étude Technique

## Contexte

- **Ticket** : MKP-XXX
- **Description** : [description du besoin et de la question à explorer]

## Instructions

### 1. Cadrage

- Reformuler la question centrale en une phrase
- Lister les hypothèses de départ (max 3-5)
- Identifier les zones d'incertitude

### 2. Exploration

- Rechercher dans la codebase existante les éléments liés au sujet
- Identifier les dépendances impactées (packages, services, entities, composants)
- Consulter la documentation projet (`docs/`) si pertinent
- Évaluer les approches possibles (2-3 max) avec pour chacune :
  - Description courte
  - Avantages
  - Inconvénients / risques
  - Effort estimé (S / M / L)

### 3. Recommandation

- Indiquer l'approche recommandée avec justification
- Lister les prérequis ou dépendances bloquantes
- Identifier les risques résiduels
- Proposer les prochaines étapes concrètes (tickets à créer)

### 4. Livrable

Produire un fichier Markdown synthétique dans `docs/spike/` :

- Nom : `MKP-XXX-<slug>.md`
- Structure imposée (voir template ci-dessous)
- Ton factuel, pas de jargon inutile
- Peu d'emojis (aucun dans le corps, 1-2 max dans les titres si nécessaire)

### Template de sortie

```markdown
# Spike MKP-XXX : <Titre>

**Date** : YYYY-MM-DD
**Auteur** : <nom ou "AI-assisted">
**Statut** : Terminé | En cours | Abandonné

## Contexte

<Pourquoi ce spike ? Quelle question doit être résolue ?>

## Hypothèses de départ

- H1 : ...
- H2 : ...

## Approches étudiées

### Approche A : <nom>

- **Description** : ...
- **Avantages** : ...
- **Inconvénients** : ...
- **Effort** : S | M | L

### Approche B : <nom>

- **Description** : ...
- **Avantages** : ...
- **Inconvénients** : ...
- **Effort** : S | M | L

## Recommandation

<Approche retenue + justification courte>

## Risques résiduels

| Risque | Probabilité | Impact | Mitigation |
| ------ | ----------- | ------ | ---------- |
| ...    | ...         | ...    | ...        |

## Prochaines étapes

1. ...
2. ...
3. ...
```

## Exemple d'utilisation

```
Ticket : MKP-312
Description : Évaluer si Symfony Notifier peut remplacer notre système custom d'envoi d'emails et SMS
```

## Conventions

- Le fichier produit ne contient pas de code d'implémentation
- Rester au niveau fonctionnel et architectural, pas de spécification technique détaillée
- Commit : `MKP-XXX: docs(spike): <description in English>`
