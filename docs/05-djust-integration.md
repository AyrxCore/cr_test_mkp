# 05 - Intégration API Djust

## 🌐 Vue d'ensemble

L'API Djust est le **cœur du système** pour la gestion des :
- 🛍️ Produits et catalogue
- 🛒 Paniers et commandes
- 👥 Comptes clients (CustomerAccounts)
- 🏢 Adresses de livraison et facturation
- 🏷️ Sellers et offres

```
┌─────────────────────────────────────────────────────────────┐
│                     MarketPlace                              │
├─────────────────────────────────────────────────────────────┤
│                                                              │
│  ┌────────────────────────────────────────────────────────┐ │
│  │                 Services Djust                          │ │
│  │                                                         │ │
│  │  DjustProductService      DjustCartService             │ │
│  │  DjustOrderService        DjustCustomerAccountService  │ │
│  │  DjustAddressService      DjustAuthenticationService   │ │
│  │  DjustSellerService       DjustCategoryService         │ │
│  │  DjustOperatorApiService  DjustAccountApiService       │ │
│  │                                                         │ │
│  └───────────────────────┬────────────────────────────────┘ │
│                          │                                   │
│                          ▼                                   │
│  ┌────────────────────────────────────────────────────────┐ │
│  │             DjustHttpClientService                      │ │
│  │                                                         │ │
│  │  • Gestion des tokens (Operator/Account)               │ │
│  │  • Refresh automatique du token Account                │ │
│  │  • Cache Symfony pour token Operator                   │ │
│  │  • Logging des requêtes                                │ │
│  │  • Support multi-tenant (Store View)                   │ │
│  └───────────────────────┬────────────────────────────────┘ │
│                          │                                   │
└──────────────────────────┼───────────────────────────────────┘
                           │
                           ▼
                   ┌───────────────┐
                   │  API Djust    │
                   │  (Externe)    │
                   └───────────────┘
```

## 🔑 Deux modes d'authentification

### Mode Operator (Admin)
- **Token stocké** : Symfony Cache (`djust_operator_token`)
- **Durée de vie** : 240 secondes
- **Credentials** : Variables d'env `DJUST_API_USERNAME` / `DJUST_API_PASSWORD`
- **Usage** : Opérations back-office, sync, batch processing

### Mode Account (Buyer)
- **Token stocké** : Session PHP (`djust_account_access_token`, `djust_account_refresh_token`)
- **Durée de vie** : Variable (avec refresh automatique)
- **Credentials** : Stockés par Account (chiffrés dans BDD)
- **Usage** : Requêtes utilisateur (panier, commandes, profil)

## 📋 Services Djust disponibles

### DjustProductService

Gestion du catalogue produits.

```php
// src/Service/Djust/DjustProductService.php
class DjustProductService
{
    // Récupérer un produit par ID
    public function getProductById(
        string $productId,
        string $idType = DjustIdType::EXTERNAL_ID->value,  // EXTERNAL_ID | INTERNAL_ID
        string $locale = DjustDefaults::LOCALE->value,     // fr_FR
    ): array;
    
    // Récupérer les offres d'un produit
    public function getProductOffers(
        string $productId,
        string $productIdType = DjustIdType::EXTERNAL_ID->value,
        string $locale = DjustDefaults::LOCALE->value,
        string $currency = DjustDefaults::CURRENCY->value,  // EUR
    ): array;
    
    // Récupérer un produit complet (produit + offres)
    public function getFullProduct(
        string $productId,
        string $productIdType = DjustIdType::EXTERNAL_ID->value,
        string $locale = DjustDefaults::LOCALE->value,
        string $currency = DjustDefaults::CURRENCY->value,
    ): array;
}
```

**Exemple d'utilisation :**
```php
// Récupérer un produit avec ses offres
$fullProduct = $this->djustProductService->getFullProduct(
    productId: 'PROD-123',
    productIdType: DjustIdType::EXTERNAL_ID->value,
    locale: 'fr_FR',
    currency: 'EUR'
);

// Seulement les offres
$offers = $this->djustProductService->getProductOffers(
    productId: 'PROD-123',
    productIdType: DjustIdType::EXTERNAL_ID->value
);
```

---

### DjustCartService

Gestion du panier.

```php
// src/Service/Djust/DjustCartService.php
class DjustCartService
{
    // Récupérer le panier actuel (ou en créer un si inexistant)
    public function getCart(): ?array;
    
    // Créer un nouveau panier
    public function createCart(): array;
    
    // Mettre à jour les items du panier (ajouter/modifier/supprimer)
    public function updateCartItems(string $cartId, array $cartItems): array;
    
    // Synchroniser le panier commercial
    public function syncCommercialOrder(string $cartId): array;
    
    // Définir les adresses de livraison et facturation
    public function setCartAddresses(
        string $cartId,
        string $shippingAddressId,
        string $billingAddressId
    ): array;
    
    // Valider le panier (transformer en commande)
    public function validateCart(string $cartId): array;
}
```

**Exemple d'utilisation :**
```php
// Récupérer ou créer le panier
$cart = $this->djustCartService->getCart();

// Ajouter des items
$cartItems = [
    new CartItem(
        action: DjustCartItemAction::ADD->value,
        offerPriceId: '12345',
        quantity: 2
    ),
];
$updatedCart = $this->djustCartService->updateCartItems($cart['id'], $cartItems);

// Définir les adresses
$this->djustCartService->setCartAddresses(
    cartId: $cart['id'],
    shippingAddressId: 'addr-123',
    billingAddressId: 'addr-456'
);

// Valider le panier
$order = $this->djustCartService->validateCart($cart['id']);
```

---

### DjustOrderService

Gestion des commandes.

```php
// src/Service/Djust/DjustOrderService.php
class DjustOrderService
{
    // Liste des commandes du buyer (avec filtres)
    public function getOrders(array $params = []): array;
    
    // Commande la plus récente
    public function getMostRecentBuyerOrder(): ?array;
    
    // Détail d'une commande par ID
    public function getOrderById(string $orderId): ?array;
    
    // Récupérer une commande pour un Account spécifique
    public function getOrderByIdForAccount(string $orderId, ?Account $account): ?array;
    
    // Télécharger un document (facture, bon de livraison...)
    public function downloadDocument(string $documentId): array;
}
```

**Exemple d'utilisation :**
```php
// Liste des commandes (paginées, triées)
$orders = $this->djustOrderService->getOrders([
    'page' => 0,
    'size' => 20,
    'sort' => 'createdAt:desc',
]);

// Détail d'une commande
$order = $this->djustOrderService->getOrderById('0000123456');

// Commande la plus récente
$lastOrder = $this->djustOrderService->getMostRecentBuyerOrder();
```

---

### DjustCustomerAccountService

Gestion des comptes clients.

```php
// src/Service/Djust/DjustCustomerAccountService.php
class DjustCustomerAccountService
{
    // Récupérer les infos du CustomerAccount (avec cache session)
    public function getCustomerAccount(bool $forceRefresh = false): ?array;
    
    // Extraire les tags utilisateur du CustomerAccount
    public function getUserTags(): array;
}
```

**Structure CustomerAccount :**
```php
[
    'id' => '0000092247',              // ID Djust du customer account
    'email' => 'user@example.com',
    'firstName' => 'John',
    'lastName' => 'Doe',
    'customerTags' => ['VIP', 'B2B'],  // Tags métier
    'customFields' => [...],           // Champs custom
]
```

**Exemple d'utilisation :**
```php
// Récupérer le customer account (cachée 60s en session)
$customerAccount = $this->djustCustomerAccountService->getCustomerAccount();

// Forcer le refresh
$customerAccount = $this->djustCustomerAccountService->getCustomerAccount(forceRefresh: true);

// Récupérer uniquement les tags
$tags = $this->djustCustomerAccountService->getUserTags();
```

---

### DjustAddressService

Gestion des adresses.

```php
// src/Service/Djust/DjustAddressService.php
class DjustAddressService
{
    // Liste des adresses du buyer
    public function getAddresses(): array;
    
    // Récupérer une adresse par ID
    // Note: L'API Djust ne supporte pas GET /addresses/:id
    // On récupère la collection puis filtre par ID
    public function getAddress(string $addressId): array;
    
    // Créer une adresse
    public function createAddress(Address $address): array;
    
    // Mettre à jour une adresse
    public function updateAddress(Address $address): array;
    
    // Supprimer une adresse
    public function deleteAddress(string $addressId): array;
}
```

**Exemple d'utilisation :**
```php
// Liste des adresses
$addresses = $this->djustAddressService->getAddresses();

// Créer une adresse
$address = new Address();
$address->setFullName('John Doe');
$address->setAddress('123 Rue de la Paix');
$address->setZipCode('75001');
$address->setCity('Paris');
$address->setCountry('FR');
$address->setPhone('0612345678');
$address->setShipping(true);
$address->setBilling(false);

$createdAddress = $this->djustAddressService->createAddress($address);

// Mettre à jour
$address->setId('addr-123');
$address->setCity('Lyon');
$updatedAddress = $this->djustAddressService->updateAddress($address);

// Supprimer
$this->djustAddressService->deleteAddress('addr-123');
```

---

### DjustSellerService

Gestion des sellers (fournisseurs).

```php
// src/Service/Djust/DjustSellerService.php
class DjustSellerService
{
    // Récupérer les sellers valides pour l'utilisateur
    public function getValidSellers(
        ?string $customerAccountId = null,
        ?DjustSearchParams $params = null
    ): array;
    
    // Récupérer tous les sellers (cachés 5min)
    public function getAllSellers(?string $customerAccountId = null): array;
    
    // Map seller ID → tarif ID
    public function getAdherentSellerTarifIdMap(?DjustSearchParams $params = null): array;
}
```

---

### DjustOperatorApiService

Service pour les appels niveau Operator (admin, sans session utilisateur).

```php
// src/Service/Djust/DjustOperatorApiService.php
class DjustOperatorApiService
{
    // Vérifier si l'API Djust est configurée
    public function isConfigured(): bool;
    
    // Trouver un utilisateur par email
    public function findCustomerUserByEmail(string $email): ?array;
    
    // Récupérer tous les utilisateurs (paginé)
    public function fetchAllCustomerUsers(): array;
    
    // Créer un customer user
    public function createCustomerUser(array $userData): array;
    
    // Mettre à jour un customer user
    public function updateCustomerUser(string $userId, array $userData): array;
}
```

**Exemple d'utilisation :**
```php
// Rechercher un utilisateur par email
$user = $this->djustOperatorApiService->findCustomerUserByEmail('user@example.com');

// Créer un nouvel utilisateur
$newUser = $this->djustOperatorApiService->createCustomerUser([
    'email' => 'newuser@example.com',
    'firstName' => 'Jane',
    'lastName' => 'Doe',
    'customerAccountId' => '0000092247',
]);
```

---

## 🔧 DjustHttpClientService - La classe de base

### Méthodes principales

```php
public function get(
    string $endpoint,
    array $queryParams = [],
    array $headers = [],
    bool $isOperator = false     // Utiliser le token Operator
): array;

public function post(
    string $endpoint,
    array $data = [],
    array $headers = [],
    bool $isOperator = false
): array;

public function put(
    string $endpoint,
    array $data = [],
    array $headers = [],
    bool $isOperator = false
): array;

public function delete(
    string $endpoint,
    array $headers = [],
    bool $isOperator = false
): array;
```

### Gestion automatique des tokens

```php
// Mode Account (par défaut) - Token en session
$response = $this->djustHttpClient->get('/shop/cart');

// Mode Operator - Token Admin depuis cache
$response = $this->djustHttpClient->get(
    '/operator/customer-users',
    isOperator: true
);
```

### Refresh automatique du token Account

Si le token Account est expiré, le service :
1. Tente d'utiliser le `refreshToken`
2. Si le refresh échoue, obtient un nouveau token avec username/password
3. Met à jour les tokens en session
4. Réessaye la requête

### Support multi-tenant (Store View)

Pour les requêtes nécessitant un contexte Channel/Adherent :

```php
// Construction des headers Store View
$headers = $this->storeViewHeadersBuilder->build();

// Requête avec Store View
$cart = $this->djustHttpClient->get(
    DjustApiEndpoint::SHOP_COMMERCIAL_ORDERS->value,
    ['isValidated' => 'false'],
    $headers  // ← Headers Store View
);
```

---

## 📡 Endpoints Djust couramment utilisés

### Produits

| Endpoint | Description |
|----------|-------------|
| `GET /shop/products/{id}` | Détail d'un produit |
| `GET /shop/products/{id}/offers` | Offres d'un produit |
| `POST /shop/search/products` | Recherche de produits |
| `GET /shop/categories` | Liste des catégories |

### Panier

| Endpoint | Description |
|----------|-------------|
| `GET /shop/commercial-orders` | Paniers (isValidated=false) |
| `POST /shop/commercial-orders` | Créer un panier |
| `PUT /shop/commercial-orders/{id}/items` | Modifier items du panier |
| `PUT /shop/commercial-orders/{id}/sync` | Synchroniser le panier |
| `PUT /shop/commercial-orders/{id}/addresses` | Définir les adresses |
| `PUT /shop/commercial-orders/{id}/validate` | Valider le panier |

### Commandes

| Endpoint | Description |
|----------|-------------|
| `GET /shop/commercial-orders` | Liste des commandes (isValidated=true) |
| `GET /shop/commercial-orders/{id}` | Détail d'une commande |
| `GET /shop/documents/{id}/download` | Télécharger un document |

### Customer Account

| Endpoint | Description |
|----------|-------------|
| `GET /shop/customer-accounts` | Infos du customer account |

### Adresses

| Endpoint | Description |
|----------|-------------|
| `GET /shop/addresses` | Liste des adresses |
| `POST /shop/addresses` | Créer une adresse |
| `PUT /shop/addresses/{id}` | Mettre à jour une adresse |
| `DELETE /shop/addresses/{id}` | Supprimer une adresse |

### Operator (Admin)

| Endpoint | Description |
|----------|-------------|
| `GET /operator/customer-users` | Liste des utilisateurs |
| `POST /operator/customer-users` | Créer un utilisateur |
| `PUT /operator/customer-users/{id}` | Mettre à jour un utilisateur |

---

## 🧪 Mocker l'API Djust dans les tests

Les mocks sont dans `tests/Resources/` :

```php
// tests/DjustMockClientCallback.php
class DjustMockClientCallback
{
    public function __invoke(string $method, string $url, array $options): ResponseInterface
    {
        // Retourne des réponses mockées selon l'URL
        return match(true) {
            str_contains($url, '/shop/cart') => $this->mockCart(),
            str_contains($url, '/shop/products') => $this->mockProduct(),
            str_contains($url, '/shop/commercial-orders') => $this->mockOrders(),
            default => throw new \Exception("URL non mockée: $url"),
        };
    }
    
    private function mockCart(): ResponseInterface
    {
        return new MockResponse(json_encode([
            'content' => [
                [
                    'id' => 'cart-123',
                    'productCount' => 3,
                    'totalPrice' => 150.00,
                    // ...
                ],
            ],
        ]));
    }
}
```

**Utilisation dans les tests :**
```php
$mockClient = new MockHttpClient(new DjustMockClientCallback());
$djustHttpClient = new DjustHttpClientService(
    httpClient: $mockClient,
    // ... autres dépendances
);
```

---

## ⚙️ Configuration

Variables d'environnement requises :

```env
# .env.local
DJUST_API_BASE_URL=https://djust-api.pre-prod.djust-app.com/qantis
DJUST_API_USERNAME=dev@qantis.co
DJUST_API_PASSWORD=your_password_here
```

---

## ⚠️ Points d'attention

### 1. **Gestion des tokens**
- Token Operator : Cache Symfony (240s)
- Token Account : Session PHP (refresh automatique)
- **Attention** : Session PHP ne scale pas horizontalement sans sticky sessions

### 2. **Credentials chiffrés**
- Les credentials Account (username/password) sont chiffrés en BDD
- Utiliser `CredentialEncryptionService` pour chiffrer/déchiffrer

### 3. **Store View (Multi-tenant)**
- Certaines requêtes nécessitent les headers Store View
- Utiliser `DjustStoreViewHeadersBuilder->build()`
- Headers : `X-DJUST-STORE-VIEW`, `X-DJUST-CUSTOMER-ACCOUNT-ID`

### 4. **Cache Session CustomerAccount**
- Le `CustomerAccount` est caché 60s en session
- Utiliser `forceRefresh: true` si besoin de données fraîches

### 5. **Adresses**
- L'API Djust ne supporte pas `GET /addresses/{id}`
- Le service récupère toutes les adresses puis filtre par ID

### 6. **IDs Djust**
- Les IDs Djust sont des **strings** (ex: `"0000092247"`)
- Ne pas les typer en `int`

### 7. **Rate limiting**
- L'API Djust a des limites de requêtes
- Utiliser le cache quand possible

### 8. **Logs**
- Les requêtes sont loguées dans `djust.log`
- Channel logger : `djustLogger`

---

## 🔄 Migration Uppler → Djust

**Principales différences :**

| Aspect | Uppler | Djust |
|--------|--------|-------|
| **Structure données** | `subaccount` | `customerAccount` |
| **Type ID** | `int` | `string` |
| **Token storage** | Fichier + Session | Cache + Session |
| **Auth** | OAuth2 client_id/secret | Username/Password |
| **Panier** | `/buyer/cart` | `/shop/commercial-orders` |
| **Multi-tenant** | Headers custom | Store View headers |

**Classes de base :**
- Uppler : `AbstractUpplerService`
- Djust : `DjustHttpClientService` (composition, pas héritage)

---

## 📚 Références

- **Endpoints** : `src/Enum/Djust/DjustApiEndpoint.php`
- **Defaults** : `src/Enum/Djust/DjustDefaults.php`
- **ID Types** : `src/Enum/Djust/DjustIdType.php`
- **Custom Fields** : `src/Enum/Djust/DjustCustomField.php`
- **Search** : `src/Service/Djust/Search/`
- **Tests** : `tests/DjustMockClientCallback.php`
