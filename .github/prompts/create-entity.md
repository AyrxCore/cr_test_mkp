# Créer une Entity Doctrine + API Platform

## Spécifications

- **Nom** : [NOM]
- **Propriétés** : [liste des propriétés avec types]
- **Relations** : [relations éventuelles avec cardinalité]

## Instructions

### 1. Entity (`src/Entity/[NOM].php`)

- `declare(strict_types=1);`
- UUID comme identifiant (`UuidGenerator`)
- Constructor property promotion avec `readonly` pour les services, pas pour les entities
- Attributs PHP 8 uniquement (`#[ORM\...]`, `#[Groups(...)]`, `#[Assert\...]`) — pas de commentaires
- PSR-12
- `#[ApiResource]` avec :
  - Operations explicites (`Get`, `GetCollection`, `Post`, `Put`, `Delete`)
  - `normalizationContext` / `denormalizationContext` avec groups de sérialisation
  - `security` sur chaque operation sensible (`is_granted('ROLE_...')`)
- Relations Doctrine via `Collection` avec `ArrayCollection()` dans le constructeur
- Ajouter les méthodes `add/remove` pour les relations `OneToMany` / `ManyToMany`
- Contraintes de validation (`#[Assert\NotBlank]`, `#[Assert\Email]`, `#[Assert\Length]`, etc.)
- Soft delete via Gedmo si pertinent (`#[Gedmo\SoftDeleteable]`)

### 2. Repository (`src/Repository/[NOM]Repository.php`)

- Étend `ServiceEntityRepository<[NOM]>`
- Ajouter les méthodes de requête custom nécessaires au métier

### 3. Factory (`src/DataFixtures/Factory/[NOM]Factory.php`)

- Étend `PersistentProxyObjectFactory`
- Méthode `defaults()` avec des données réalistes via Faker

### 4. Migration

- Exécuter `make database-diff` pour générer la migration
- Exécuter `make database-migrations` pour appliquer la migration

## Exemple d'utilisation

```
Crée une entity "Catalogue" avec :
- name (string, required, max 255)
- description (text, nullable)
- active (boolean, default true)
- Relation ManyToMany vers Adherent
- Relation ManyToMany vers Accord
```

## Validation

```bash
make lint
make all-tests-parallel
```
