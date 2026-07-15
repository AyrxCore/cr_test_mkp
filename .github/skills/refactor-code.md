# Skill: Refactorer du Code

## Identité

Tu es un expert en refactoring. Tu améliores la structure du code sans changer son comportement, en appliquant des techniques éprouvées.

## Règle d'Or

```
Le refactoring ne change JAMAIS le comportement externe.
Les tests doivent passer avant ET après.
```

## Workflow de Refactoring

```
┌─────────────────────────────────────────┐
│  1. TESTS VERTS                         │
│     Vérifier que tous les tests passent │
│                  │                       │
│                  ▼                       │
│  2. IDENTIFIER                          │
│     Repérer le code smell               │
│                  │                       │
│                  ▼                       │
│  3. REFACTORER                          │
│     Appliquer la technique appropriée   │
│                  │                       │
│                  ▼                       │
│  4. TESTS VERTS                         │
│     Vérifier que les tests passent      │
│                  │                       │
│                  ▼                       │
│  5. COMMIT                              │
│     Un commit par refactoring           │
└─────────────────────────────────────────┘
```

## Techniques de Refactoring

### 1. Extract Method

**Quand** : Fonction trop longue, code dupliqué, commentaire explicatif

```php
// ❌ Avant
public function processAdherent(Adherent $adherent): void
{
    // Validate adherent data
    if (empty($adherent->getEmail())) {
        throw new InvalidArgumentException('Email required');
    }
    if (!filter_var($adherent->getEmail(), FILTER_VALIDATE_EMAIL)) {
        throw new InvalidArgumentException('Invalid email');
    }

    // Calculate rights
    $rights = [];
    foreach ($adherent->getAccords() as $accord) {
        if ($accord->isActive()) {
            $rights[] = $accord->getOffre();
        }
    }

    // Save
    $this->repository->save($adherent);
}

// ✅ Après
public function processAdherent(Adherent $adherent): void
{
    $this->validateAdherent($adherent);
    $this->calculateRights($adherent);
    $this->repository->save($adherent);
}

private function validateAdherent(Adherent $adherent): void
{
    if (empty($adherent->getEmail())) {
        throw new InvalidArgumentException('Email required');
    }
    if (!filter_var($adherent->getEmail(), FILTER_VALIDATE_EMAIL)) {
        throw new InvalidArgumentException('Invalid email');
    }
}

private function calculateRights(Adherent $adherent): array
{
    return $adherent->getAccords()
        ->filter(fn(Accord $a) => $a->isActive())
        ->map(fn(Accord $a) => $a->getOffre())
        ->toArray();
}
```

### 2. Extract Class

**Quand** : Classe avec trop de responsabilités (God Class)

```php
// ❌ Avant - Classe qui fait tout
class AdherentService
{
    public function create(array $data): Adherent {}
    public function validate(Adherent $a): bool {}
    public function calculateTarif(Adherent $a): Money {}
    public function sendWelcomeEmail(Adherent $a): void {}
    public function generateCard(Adherent $a): Pdf {}
}

// ✅ Après - Responsabilités séparées
class AdherentService
{
    public function __construct(
        private AdherentValidator $validator,
        private TarifCalculator $calculator,
    ) {}

    public function create(array $data): Adherent {}
}

class AdherentValidator
{
    public function validate(Adherent $a): bool {}
}

class TarifCalculator
{
    public function calculate(Adherent $a): Money {}
}

class AdherentNotifier
{
    public function sendWelcome(Adherent $a): void {}
}
```

### 3. Replace Conditional with Polymorphism

**Quand** : Switch/if-else basés sur un type

```php
// ❌ Avant
class TarifCalculator
{
    public function calculate(Tarif $tarif): Money
    {
        return match ($tarif->getType()) {
            'standard' => $tarif->getBasePrice(),
            'premium' => $tarif->getBasePrice()->multiply(1.5),
            'enterprise' => $this->calculateEnterprise($tarif),
            default => throw new InvalidArgumentException(),
        };
    }
}

// ✅ Après - Polymorphisme
interface TarifCalculatorInterface
{
    public function calculate(Tarif $tarif): Money;
}

class StandardTarifCalculator implements TarifCalculatorInterface
{
    public function calculate(Tarif $tarif): Money
    {
        return $tarif->getBasePrice();
    }
}

class PremiumTarifCalculator implements TarifCalculatorInterface
{
    public function calculate(Tarif $tarif): Money
    {
        return $tarif->getBasePrice()->multiply(1.5);
    }
}

// Utilisation avec un Factory ou Service Locator
```

### 4. Introduce Parameter Object

**Quand** : Trop de paramètres (> 3)

```php
// ❌ Avant
public function search(
    string $nom,
    string $email,
    ?DateTimeInterface $dateDebut,
    ?DateTimeInterface $dateFin,
    bool $activeOnly,
    int $limit,
    int $offset
): array {}

// ✅ Après
final readonly class AdherentSearchCriteria
{
    public function __construct(
        public string $nom = '',
        public string $email = '',
        public ?DateTimeInterface $dateDebut = null,
        public ?DateTimeInterface $dateFin = null,
        public bool $activeOnly = true,
        public int $limit = 20,
        public int $offset = 0,
    ) {}
}

public function search(AdherentSearchCriteria $criteria): array {}
```

### 5. Replace Magic Numbers/Strings

**Quand** : Valeurs littérales sans signification claire

```php
// ❌ Avant
if ($adherent->getStatus() === 1) {}
if ($discount > 50) {}
$timeout = 3600;

// ✅ Après
enum AdherentStatus: int
{
    case ACTIVE = 1;
    case INACTIVE = 2;
    case SUSPENDED = 3;
}

final class TarifRules
{
    public const MAX_DISCOUNT_PERCENT = 50;
    public const SESSION_TIMEOUT_SECONDS = 3600;
}

if ($adherent->getStatus() === AdherentStatus::ACTIVE) {}
if ($discount > TarifRules::MAX_DISCOUNT_PERCENT) {}
```

### 6. Move Method to Appropriate Class

**Quand** : Feature Envy (méthode utilise trop une autre classe)

```php
// ❌ Avant - Service qui manipule trop l'entité
class AccordService
{
    public function isExpired(Accord $accord): bool
    {
        return $accord->getDateFin() !== null
            && $accord->getDateFin() < new DateTimeImmutable()
            && $accord->getStatus() !== AccordStatus::RENEWED;
    }
}

// ✅ Après - Logique dans l'entité
class Accord
{
    public function isExpired(): bool
    {
        return $this->dateFin !== null
            && $this->dateFin < new DateTimeImmutable()
            && $this->status !== AccordStatus::RENEWED;
    }
}
```

### 7. Guard Clauses (Replace Nested Conditionals)

**Quand** : Nesting profond, conditions complexes

```php
// ❌ Avant
public function process(Adherent $adherent): void
{
    if ($adherent !== null) {
        if ($adherent->isActive()) {
            if ($adherent->hasAccords()) {
                // Logique principale
                $this->doProcess($adherent);
            }
        }
    }
}

// ✅ Après - Guard clauses
public function process(Adherent $adherent): void
{
    if ($adherent === null) {
        return;
    }

    if (!$adherent->isActive()) {
        return;
    }

    if (!$adherent->hasAccords()) {
        return;
    }

    $this->doProcess($adherent);
}
```

## Refactoring Vue/TypeScript

### Extract Composable

```typescript
// ❌ Avant - Logique dans le composant
const isLoading = ref(false)
const error = ref<string | null>(null)
const data = ref<IAdherent[]>([])

const fetchData = async () => {
  isLoading.value = true
  error.value = null
  try {
    data.value = await adherentApi.getAll()
  } catch (e) {
    error.value = 'Erreur de chargement'
  } finally {
    isLoading.value = false
  }
}

// ✅ Après - Composable réutilisable
// composables/useAsyncData.ts
export function useAsyncData<T>(fetcher: () => Promise<T>) {
  const data = ref<T | null>(null)
  const isLoading = ref(false)
  const error = ref<string | null>(null)

  const execute = async () => {
    isLoading.value = true
    error.value = null
    try {
      data.value = await fetcher()
    } catch (e) {
      error.value = 'Erreur de chargement'
    } finally {
      isLoading.value = false
    }
  }

  return { data, isLoading, error, execute }
}

// Utilisation
const {
  data: adherents,
  isLoading,
  error,
  execute: fetchAdherents,
} = useAsyncData(() => adherentApi.getAll())
```

### Extract Component

```vue
<!-- ❌ Avant - Template trop long -->
<template>
  <div>
    <div class="card">
      <h3>{{ adherent.nom }}</h3>
      <p>{{ adherent.email }}</p>
      <span :class="statusClass">{{ statusLabel }}</span>
      <button @click="edit">Modifier</button>
    </div>
    <!-- Répété plusieurs fois... -->
  </div>
</template>

<!-- ✅ Après - Composant extrait -->
<!-- AdherentCard.vue -->
<template>
  <div class="card">
    <h3>{{ adherent.nom }}</h3>
    <p>{{ adherent.email }}</p>
    <AdherentStatusBadge :status="adherent.status" />
    <button @click="emit('edit')">Modifier</button>
  </div>
</template>
```

## Code Smells → Technique

| Code Smell             | Technique de Refactoring                   |
| ---------------------- | ------------------------------------------ |
| Long Method            | Extract Method                             |
| Large Class            | Extract Class                              |
| Long Parameter List    | Introduce Parameter Object                 |
| Duplicated Code        | Extract Method / Extract Class             |
| Feature Envy           | Move Method                                |
| Data Clumps            | Extract Class / Introduce Parameter Object |
| Primitive Obsession    | Replace with Value Object                  |
| Switch Statements      | Replace with Polymorphism                  |
| Parallel Inheritance   | Collapse Hierarchy                         |
| Lazy Class             | Inline Class                               |
| Speculative Generality | Remove unused abstractions                 |
| Comments               | Rename / Extract Method                    |

## Commandes de Validation

```bash
# AVANT le refactoring
make all-tests-parallel    # Doit être vert

# APRÈS chaque étape
make lint                  # Code propre
make all-tests-parallel    # Doit rester vert

# Proposer ce message de commit (ne jamais exécuter git commit automatiquement) :
# MKP-XXX: refactor(<scope>): extract TarifCalculator from AdherentService
```

## Instructions

1. **TOUJOURS** avoir des tests verts avant de commencer
2. **TOUJOURS** vérifier les tests après chaque modification
3. **JAMAIS** changer le comportement pendant un refactoring
4. **UN** refactoring à la fois, **UN** commit par refactoring
5. Nommer les commits clairement en anglais : `MKP-XXX: refactor(<scope>): extract X from Y`
6. Si les tests échouent → rollback immédiat
