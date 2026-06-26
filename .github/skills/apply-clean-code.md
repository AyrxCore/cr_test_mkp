# Skill: Appliquer le Clean Code

## Identité

Tu es un expert en Clean Code. Tu écris du code lisible, maintenable et expressif qui se lit comme de la prose.

## Principes Fondamentaux

### 1. Noms Significatifs

```php
// ❌ Mauvais
$d = 30; // elapsed time in days
$list1 = [];
function process($data) {}

// ✅ Bon
$elapsedDays = 30;
$activeAdherents = [];
function calculateTarifDiscount(Tarif $tarif): float {}
```

### 2. Fonctions Courtes et Focalisées

```php
// ❌ Mauvais - Fonction qui fait trop de choses
public function processAdherent(Adherent $adherent): void
{
    // Valide l'adhérent
    if (empty($adherent->getEmail())) {
        throw new InvalidArgumentException('Email required');
    }
    // Calcule les droits
    $droits = [];
    foreach ($adherent->getAccords() as $accord) {
        if ($accord->isActive()) {
            $droits[] = $accord->getOffre();
        }
    }
    // Envoie une notification
    $this->mailer->send($adherent->getEmail(), 'Bienvenue');
    // Sauvegarde
    $this->repository->save($adherent);
}

// ✅ Bon - Fonctions séparées avec responsabilité unique
public function processAdherent(Adherent $adherent): void
{
    $this->validateAdherent($adherent);
    $this->calculateDroits($adherent);
    $this->sendWelcomeNotification($adherent);
    $this->repository->save($adherent);
}

private function validateAdherent(Adherent $adherent): void
{
    if (empty($adherent->getEmail())) {
        throw new InvalidArgumentException('Email required');
    }
}

private function calculateDroits(Adherent $adherent): array
{
    return $adherent->getAccords()
        ->filter(fn(Accord $accord) => $accord->isActive())
        ->map(fn(Accord $accord) => $accord->getOffre())
        ->toArray();
}
```

### 3. Pas de Commentaires - Code Auto-Explicatif

```php
// ❌ Mauvais - Commentaire qui explique du code obscur
// Check if adherent can access the offer based on active accords
if ($a->getAcc()->filter(fn($x) => $x->getS() === 1)->count() > 0) {}

// ✅ Bon - Code qui s'explique lui-même
if ($adherent->hasActiveAccordFor($offre)) {}
```

### 4. Early Return

```php
// ❌ Mauvais - Nesting profond
public function canAccess(Adherent $adherent, Offre $offre): bool
{
    if ($adherent->isActive()) {
        if ($adherent->hasAccords()) {
            foreach ($adherent->getAccords() as $accord) {
                if ($accord->getOffre()->getId() === $offre->getId()) {
                    if ($accord->isActive()) {
                        return true;
                    }
                }
            }
        }
    }
    return false;
}

// ✅ Bon - Early returns
public function canAccess(Adherent $adherent, Offre $offre): bool
{
    if (!$adherent->isActive()) {
        return false;
    }

    if (!$adherent->hasAccords()) {
        return false;
    }

    return $adherent->getAccords()
        ->filter(fn(Accord $a) => $a->isActive() && $a->getOffre()->equals($offre))
        ->isNotEmpty();
}
```

### 5. Tell, Don't Ask

```php
// ❌ Mauvais - Demander l'état puis décider
if ($tarif->getStatus() === TarifStatus::DRAFT) {
    $tarif->setStatus(TarifStatus::PUBLISHED);
    $tarif->setPublishedAt(new DateTimeImmutable());
}

// ✅ Bon - Dire à l'objet quoi faire
$tarif->publish();

// Dans l'entité :
public function publish(): void
{
    if ($this->status !== TarifStatus::DRAFT) {
        throw new DomainException('Cannot publish non-draft tarif');
    }
    $this->status = TarifStatus::PUBLISHED;
    $this->publishedAt = new DateTimeImmutable();
}
```

## Règles pour PHP/Symfony

### Structure de Classe

```php
<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Tarif;
use App\Repository\TarifRepository;

final readonly class TarifService
{
    public function __construct(
        private TarifRepository $tarifRepository,
        private TarifCalculator $calculator,
    ) {
    }

    public function calculateTotal(Tarif $tarif): Money
    {
        $basePrice = $tarif->getBasePrice();
        $discount = $this->calculator->getDiscount($tarif);

        return $basePrice->subtract($discount);
    }
}
```

### Conventions

| Règle | Exemple |
|-------|---------|
| Classes `final` par défaut | `final class AdherentService` |
| `readonly` pour l'immutabilité | `final readonly class` |
| Constructor promotion | `private TarifRepository $repo` |
| Return types explicites | `: void`, `: Tarif`, `: ?string` |
| Pas de `else` après `return` | Early return pattern |

## Règles pour Vue/TypeScript

### Structure de Composant

```vue
<script setup lang="ts">
// 1. Imports
import { ref, computed, onMounted } from 'vue';
import { useAdherentStore } from '@/stores/adherentStore';

// 2. Types/Interfaces
interface Props {
  adherentId: number;
}

// 3. Props/Emits
const props = defineProps<Props>();
const emit = defineEmits<{
  (e: 'update'): void;
}>();

// 4. Stores/Composables
const store = useAdherentStore();

// 5. State (refs)
const isLoading = ref(false);

// 6. Computed
const adherent = computed(() => store.getById(props.adherentId));

// 7. Methods
const handleUpdate = async () => {
  isLoading.value = true;
  await store.update(props.adherentId);
  emit('update');
  isLoading.value = false;
};

// 8. Lifecycle
onMounted(() => {
  store.fetchById(props.adherentId);
});
</script>

<template>
  <!-- Template clair et lisible -->
</template>
```

### Conventions TypeScript

```typescript
// ❌ Mauvais
const x: any = getData();
const items = data.filter((i) => i.a === true);

// ✅ Bon
const adherent: IAdherent = await adherentApi.getById(id);
const activeItems = items.filter((item) => item.isActive);
```

## Code Smells à Éviter

| Smell | Problème | Solution |
|-------|----------|----------|
| Long Method | Fonction > 20 lignes | Extract Method |
| Long Parameter List | > 3 paramètres | Introduce Parameter Object |
| Primitive Obsession | Trop de types primitifs | Value Objects |
| Feature Envy | Classe utilise trop une autre | Move Method |
| Data Class | Classe sans comportement | Ajouter les méthodes métier |
| Comments | Code pas clair | Renommer, refactorer |
| Magic Numbers | `if (status === 3)` | `if (status === Status::ACTIVE)` |

## Checklist Clean Code

Avant chaque commit :

- [ ] Les noms sont-ils explicites ?
- [ ] Les fonctions font-elles une seule chose ?
- [ ] Y a-t-il des commentaires à remplacer par du code clair ?
- [ ] Le nesting est-il réduit (early returns) ?
- [ ] Les classes ont-elles une seule responsabilité ?
- [ ] Pas de code dupliqué ?
- [ ] Pas de magic numbers/strings ?

## Instructions

1. **TOUJOURS** utiliser des noms explicites et prononçables
2. **TOUJOURS** garder les fonctions courtes (< 20 lignes idéalement)
3. **JAMAIS** de commentaires - le code doit s'expliquer lui-même
4. **TOUJOURS** utiliser early returns pour réduire le nesting
5. **TOUJOURS** respecter le Single Responsibility Principle
6. Refactorer immédiatement si un code smell est détecté

