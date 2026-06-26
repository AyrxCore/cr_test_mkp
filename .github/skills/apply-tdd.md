# Skill: Appliquer le TDD

## Identité

Tu es un expert en Test-Driven Development. Tu guides le développement en écrivant les tests AVANT le code de production.

## Cycle TDD

```
┌─────────────────────────────────────────┐
│            🔴 RED                        │
│     Écrire un test qui échoue           │
│                  │                       │
│                  ▼                       │
│            🟢 GREEN                      │
│  Écrire le minimum de code pour passer  │
│                  │                       │
│                  ▼                       │
│            🔵 REFACTOR                   │
│     Améliorer le code sans casser       │
│                  │                       │
│                  ▼                       │
│            (Répéter)                     │
└─────────────────────────────────────────┘
```

## Règles d'Or

1. **JAMAIS** écrire du code de production sans test qui échoue d'abord
2. **JAMAIS** écrire plus de test que nécessaire pour échouer
3. **JAMAIS** écrire plus de code que nécessaire pour passer le test

## Workflow avec Pest PHP

### Étape 1 : RED - Écrire le test

```php
<?php

declare(strict_types=1);

// tests/Unit/Service/TarifCalculatorServiceTest.php

use App\Service\TarifCalculatorService;
use App\Entity\Tarif;

describe('TarifCalculatorService', function () {
    it('calculates price with 20% discount', function () {
        // Arrange
        $calculator = new TarifCalculatorService();
        $tarif = new Tarif(basePrice: 100.00);

        // Act
        $result = $calculator->applyDiscount($tarif, 20);

        // Assert
        expect($result)->toBe(80.00);
    });
});
```

```bash
# Exécuter le test - DOIT ÉCHOUER
./vendor/bin/pest tests/Unit/Service/TarifCalculatorServiceTest.php
# ❌ Class 'App\Service\TarifCalculatorService' not found
```

### Étape 2 : GREEN - Code minimum

```php
<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Tarif;

final readonly class TarifCalculatorService
{
    public function applyDiscount(Tarif $tarif, int $discountPercent): float
    {
        return $tarif->getBasePrice() * (1 - $discountPercent / 100);
    }
}
```

```bash
# Exécuter le test - DOIT PASSER
./vendor/bin/pest tests/Unit/Service/TarifCalculatorServiceTest.php
# ✅ PASS
```

### Étape 3 : REFACTOR

- Améliorer la lisibilité
- Extraire des méthodes si nécessaire
- **Sans casser les tests**

### Étape 4 : Ajouter le cas suivant

```php
it('throws exception for negative discount', function () {
    $calculator = new TarifCalculatorService();
    $tarif = new Tarif(basePrice: 100.00);

    expect(fn() => $calculator->applyDiscount($tarif, -10))
        ->toThrow(InvalidArgumentException::class);
});
```

## Types de Tests

### Tests Unitaires (isolés)

```php
// Mocker les dépendances
use Mockery;

it('sends notification when tarif is opened', function () {
    $notifier = Mockery::mock(NotifierInterface::class);
    $notifier->shouldReceive('send')->once();

    $service = new TarifService($notifier);
    $service->open($tarif);
});
```

### Tests API (intégration)

```php
use App\Factory\AdherentFactory;

describe('POST /api/adherents', function () {
    it('creates adherent with valid data', function () {
        $response = $this->post('/api/adherents', [
            'json' => [
                'nom' => 'Dupont',
                'email' => 'dupont@example.com',
            ],
        ]);

        expect($response->getStatusCode())->toBe(201);
    });

    it('returns 422 for invalid email', function () {
        $response = $this->post('/api/adherents', [
            'json' => [
                'nom' => 'Dupont',
                'email' => 'invalid-email',
            ],
        ]);

        expect($response->getStatusCode())->toBe(422);
    });
});
```

## Assertions Fréquentes

```php
// Valeurs
expect($value)->toBe($expected);          // Strict equality
expect($value)->toEqual($expected);       // Loose equality
expect($value)->toBeNull();
expect($value)->toBeTrue();
expect($value)->toBeFalse();

// Types
expect($value)->toBeArray();
expect($value)->toBeString();
expect($value)->toBeInstanceOf(Adherent::class);

// Collections
expect($array)->toHaveCount(3);
expect($array)->toContain($item);
expect($array)->toHaveKey('id');

// Exceptions
expect(fn() => $service->process())
    ->toThrow(InvalidArgumentException::class, 'Message attendu');

// Comparaisons
expect($value)->toBeGreaterThan(10);
expect($value)->toBeBetween(1, 100);
```

## Commandes

```bash
# Tous les tests
make all-tests-parallel

# Tests unitaires uniquement
make unit-tests

# Tests API
make api-tests

# Un fichier spécifique
./vendor/bin/pest tests/Unit/Service/TarifCalculatorServiceTest.php

# Un test spécifique
./vendor/bin/pest --filter="calculates price"

# Avec coverage
./vendor/bin/pest --coverage
```

## Checklist TDD

Avant chaque implémentation :

- [ ] Ai-je écrit le test d'abord ?
- [ ] Le test échoue-t-il pour la bonne raison ?
- [ ] Mon code est-il le minimum nécessaire ?
- [ ] Ai-je refactoré après le GREEN ?
- [ ] Tous les tests passent-ils encore ?

## Anti-Patterns à Éviter

| ❌ Anti-Pattern               | ✅ Bonne Pratique                        |
| ----------------------------- | ---------------------------------------- |
| Écrire tous les tests d'abord | Un test à la fois                        |
| Tester l'implémentation       | Tester le comportement                   |
| Tests trop gros               | Tests atomiques et focalisés             |
| Copier-coller les tests       | Utiliser `dataset()` pour les variations |
| Ignorer le refactoring        | Toujours refactorer après GREEN          |

## Instructions

1. **TOUJOURS** écrire le test avant le code
2. **TOUJOURS** voir le test échouer (RED) avant d'implémenter
3. **TOUJOURS** écrire le code minimum pour passer (GREEN)
4. **TOUJOURS** refactorer après le GREEN
5. Utiliser des noms de tests descriptifs : `it('does something when condition')`
