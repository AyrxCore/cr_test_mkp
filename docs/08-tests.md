# 08 - Tests

## 🧪 Framework de tests

Le projet utilise **Pest PHP**, un framework de tests moderne et expressif basé sur PHPUnit.

### Structure des tests

```
tests/
├── Api/                    # Tests des endpoints API
├── Integration/            # Tests d'intégration
├── Unit/                   # Tests unitaires
│   ├── Channel/
│   ├── Context/
│   ├── Helper/
│   ├── Security/
│   └── Service/
├── Constraint/             # Contraintes custom pour les assertions
├── Resources/              # Données de mock (JSON, fixtures)
├── Story/                  # Stories pour les fixtures
├── ApiTestCase.php         # Classe de base pour tests API
├── IntegrationTestCase.php # Classe de base pour tests d'intégration
├── UnitTestCase.php        # Classe de base pour tests unitaires
├── MockClientCallback.php  # Mock du client HTTP Djust
├── Pest.php                # Configuration Pest
└── bootstrap.php           # Bootstrap des tests
```

### Configuration Pest

```php
// tests/Pest.php
<?php

declare(strict_types=1);

use App\Tests\ApiTestCase;
use App\Tests\IntegrationTestCase;
use App\Tests\UnitTestCase;

uses(UnitTestCase::class)->in('Unit');
uses(IntegrationTestCase::class)->in('Integration');
uses(ApiTestCase::class)->in('Api');
```

## 📝 Convention de nommage

```php
// ✅ Correct
it('should return user accounts when authenticated', function () {
    // ...
});

it('should throw exception when user not found', function () {
    // ...
});

// ❌ Incorrect
test('test get user accounts', function () {
    // ...
});
```

## 🔧 Classes de base

### UnitTestCase

Pour les tests unitaires isolés :

```php
// tests/UnitTestCase.php
class UnitTestCase extends TestCase
{
    use RefreshDatabase;
    
    protected function setUp(): void
    {
        parent::setUp();
        // Setup commun aux tests unitaires
    }
}
```

### IntegrationTestCase

Pour les tests avec accès à la base de données :

```php
// tests/IntegrationTestCase.php
class IntegrationTestCase extends KernelTestCase
{
    protected EntityManagerInterface $em;
    
    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();
        $this->em = self::getContainer()->get(EntityManagerInterface::class);
    }
}
```

### ApiTestCase

Pour les tests des endpoints API :

```php
// tests/ApiTestCase.php
class ApiTestCase extends WebTestCase
{
    protected KernelBrowser $client;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->client = static::createClient();
    }
    
    protected function authenticateAs(User $user): void
    {
        // Génère un JWT token pour l'utilisateur
        $token = $this->getContainer()
            ->get(JWTTokenManagerInterface::class)
            ->create($user);
            
        $this->client->setServerParameter('HTTP_AUTHORIZATION', 'Bearer ' . $token);
    }
}
```

## 📋 Exemples de tests

### Test unitaire

```php
// tests/Unit/Service/CartServiceTest.php
<?php

declare(strict_types=1);

use App\Service\CartService;
use App\Entity\CartSavings;

it('should calculate savings correctly', function () {
    // Arrange
    $cartService = new CartService(
        djustCartService: $this->mock(DjustCartService::class),
        em: $this->mock(EntityManagerInterface::class),
    );
    
    $cart = createCart(total: 100.00, discount: 15.00);
    
    // Act
    $savings = $cartService->calculateSavings($cart);
    
    // Assert
    expect($savings)->toBeInstanceOf(CartSavings::class);
    expect($savings->getAmount())->toBe(15.00);
    expect($savings->getPercentage())->toBe(15.0);
});

it('should return zero savings when no discount', function () {
    $cartService = new CartService(/* ... */);
    
    $cart = createCart(total: 100.00, discount: 0.00);
    
    $savings = $cartService->calculateSavings($cart);
    
    expect($savings->getAmount())->toBe(0.00);
});
```

### Test d'intégration

```php
// tests/Integration/Repository/UserRepositoryTest.php
<?php

declare(strict_types=1);

use App\Entity\User;
use App\Repository\UserRepository;

it('should find user by email', function () {
    // Arrange
    $user = createUser(email: 'test@example.com');
    $this->em->persist($user);
    $this->em->flush();
    
    $repository = $this->em->getRepository(User::class);
    
    // Act
    $found = $repository->findUserByUsernameOrEmail('test@example.com');
    
    // Assert
    expect($found)->not->toBeNull();
    expect($found->getEmail())->toBe('test@example.com');
});

it('should return null when user not found', function () {
    $repository = $this->em->getRepository(User::class);
    
    $found = $repository->findUserByUsernameOrEmail('nonexistent@example.com');
    
    expect($found)->toBeNull();
});
```

### Test API

```php
// tests/Api/AccountTest.php
<?php

declare(strict_types=1);

use App\Entity\User;
use App\Entity\Account;

it('should return user accounts when authenticated', function () {
    // Arrange
    $user = createUser();
    $account = createAccount(user: $user);
    $this->em->persist($user);
    $this->em->persist($account);
    $this->em->flush();
    
    $this->authenticateAs($user);
    
    // Act
    $this->client->request('GET', '/api/accounts');
    
    // Assert
    expect($this->client->getResponse()->getStatusCode())->toBe(200);
    
    $data = json_decode($this->client->getResponse()->getContent(), true);
    expect($data['hydra:member'])->toHaveCount(1);
    expect($data['hydra:member'][0]['id'])->toBe($account->getId()->toString());
});

it('should return 401 when not authenticated', function () {
    $this->client->request('GET', '/api/accounts');
    
    expect($this->client->getResponse()->getStatusCode())->toBe(401);
});
```

## 🎭 Mocks API Djust

### DjustMockClientCallback

```php
// tests/MockClient/DjustMockClientCallback.php
class DjustMockClientCallback
{
    public function __invoke(string $method, string $url, array $options): ResponseInterface
    {
        return match(true) {
            str_contains($url, 'oauth/token') => $this->mockOAuthToken(),
            str_contains($url, '/shop/cart') => $this->mockCart(),
            str_contains($url, '/shop/products') => $this->mockProduct($url),
            str_contains($url, '/shop/orders') => $this->mockOrders(),
            default => throw new \Exception("URL non mockée: $url"),
        };
    }
    
    private function mockCart(): MockResponse
    {
        $json = file_get_contents(__DIR__ . '/../MockData/cart.json');
        return new MockResponse($json, ['http_code' => 200]);
    }
    
    private function mockProduct(string $url): MockResponse
    {
        // Extrait l'ID du produit de l'URL
        preg_match('/products\/([^\/]+)/', $url, $matches);
        $productId = $matches[1] ?? '1';
        
        $json = file_get_contents(__DIR__ . "/../MockData/product_{$productId}.json");
        return new MockResponse($json, ['http_code' => 200]);
    }
}
```

### Fichiers de ressources

```
tests/Resources/
├── cart.json           # Mock du panier
├── product_1.json      # Mock produit ID 1
├── product_2.json      # Mock produit ID 2
├── orders.json         # Mock liste des commandes
├── categories.json     # Mock des catégories
└── company.json        # Mock de la company
```

### Configuration du mock dans les tests

```yaml
# config/services_test.yaml
services:
  Symfony\Contracts\HttpClient\HttpClientInterface:
    class: Symfony\Component\HttpClient\MockHttpClient
    arguments:
      - '@App\Tests\MockClientCallback'
```

## ▶️ Exécution des tests

### Commandes Make

```bash
# Tous les tests
make all-tests

# Tests unitaires uniquement
make unit-tests

# Tests API uniquement
make api-tests

# Tests d'intégration
make integration-tests

# Un fichier spécifique
make exec php bin/phpunit tests/Unit/Service/CartServiceTest.php

# Avec filtre
make exec php bin/phpunit --filter="should calculate savings"
```

### Directement avec Pest

```bash
# Tous les tests
docker exec -it marketplace-php-1 ./vendor/bin/pest

# Un dossier
docker exec -it marketplace-php-1 ./vendor/bin/pest tests/Unit

# Un fichier
docker exec -it marketplace-php-1 ./vendor/bin/pest tests/Unit/Service/CartServiceTest.php

# Avec couverture de code
docker exec -it marketplace-php-1 ./vendor/bin/pest --coverage
```

## ✅ Bonnes pratiques

1. **Nommage** : Toujours utiliser `it('should ...')` pour décrire le comportement attendu
2. **AAA Pattern** : Arrange, Act, Assert
3. **Isolation** : Chaque test doit être indépendant
4. **Mocks** : Mocker les services externes (Djust, Mailer...)
5. **Fixtures** : Utiliser des factories pour créer les entités de test
6. **Coverage** : Viser une couverture de code significative sur le code métier

