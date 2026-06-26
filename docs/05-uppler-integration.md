# 05 - Intégration API Uppler

## 🌐 Vue d'ensemble

L'API Uppler est le **cœur du système** pour la gestion des :
- 🛍️ Produits et catalogue
- 🛒 Paniers et commandes
- 👥 Utilisateurs et comptes buyers
- 🏢 Companies et adresses

```
┌─────────────────────────────────────────────────────────────┐
│                     MarketPlace                              │
├─────────────────────────────────────────────────────────────┤
│                                                              │
│  ┌────────────────────────────────────────────────────────┐ │
│  │                 Services Uppler                         │ │
│  │                                                         │ │
│  │  UpplerProductService     UpplerCartService            │ │
│  │  UpplerOrderService       UpplerBuyerCompanyService    │ │
│  │  UpplerAddressService     UpplerAuthenticationService  │ │
│  │  UpplerSellerService      UpplerCountryService         │ │
│  │  UpplerPartnerService     UpplerPageService            │ │
│  │                                                         │ │
│  └───────────────────────┬────────────────────────────────┘ │
│                          │                                   │
│                          ▼                                   │
│  ┌────────────────────────────────────────────────────────┐ │
│  │              AbstractUpplerService                      │ │
│  │                                                         │ │
│  │  • Gestion des tokens (Admin/User)                     │ │
│  │  • Retry automatique sur 401                           │ │
│  │  • Logging des requêtes                                │ │
│  │  • Cache HTTP optionnel                                │ │
│  └───────────────────────┬────────────────────────────────┘ │
│                          │                                   │
└──────────────────────────┼───────────────────────────────────┘
                           │
                           ▼
                   ┌───────────────┐
                   │  API Uppler   │
                   │  (Externe)    │
                   └───────────────┘
```

## 📋 Services Uppler disponibles

### UpplerProductService

Gestion du catalogue produits.

```php
// src/Service/UpplerProductService.php
class UpplerProductService extends AbstractUpplerService
{
    // Recherche de produits avec filtres
    public function findProducts(
        array $options,          // Filtres de recherche
        array $expands = [],     // Données à inclure (price, properties...)
        int $page = 1,
        int $perPage = 10,
    ): array;
    
    // Récupérer un produit par ID (en tant que buyer)
    public function findProductById(?int $productId, array $filters = []): array;
    
    // Récupérer un produit en mode Admin
    public function findProductByIdForAdmin(?int $productId): array;
    
    // Récupérer un variant par ID
    public function findVariantById(?int $variantId);
}
```

**Exemple d'utilisation :**
```php
$products = $this->upplerProductService->findProducts(
    options: [
        'filters' => [
            'category_id' => 123,
            'seller_id' => [456, 789],
        ],
    ],
    expands: ['price', 'images', 'company'],
    page: 1,
    perPage: 20
);
```

---

### UpplerCartService

Gestion du panier.

```php
// src/Service/UpplerCartService.php
class UpplerCartService extends AbstractUpplerService
{
    // Récupérer le panier actuel
    public function getCart(): array;
    
    // Ajouter un produit au panier
    public function addToCart(int $variantId, int $quantity): array;
    
    // Mettre à jour la quantité
    public function updateQuantity(int $orderItemId, int $quantity): array;
    
    // Supprimer un produit
    public function removeFromCart(int $orderItemId): bool;
    
    // Mettre à jour les adresses
    public function updateAddresses(int $cartId, int $shippingAddressId, int $billingAddressId): array;
}
```

---

### UpplerOrderService

Gestion des commandes.

```php
// src/Service/UpplerOrderService.php
class UpplerOrderService extends AbstractUpplerService
{
    // Liste des commandes du buyer
    public function getOrders(int $page = 1, int $perPage = 10): array;
    
    // Détail d'une commande
    public function getOrder(int $orderId): array;
    
    // Valider le panier (créer la commande)
    public function checkout(int $cartId): array;
}
```

---

### UpplerBuyerCompanyService

Gestion des entreprises buyers.

```php
// src/Service/UpplerBuyerCompanyService.php
class UpplerBuyerCompanyService extends AbstractUpplerService
{
    // Récupérer les infos de la company
    public function getCompany(): array;
    
    // Mettre à jour les infos
    public function updateCompany(array $data): array;
    
    // Récupérer les mandats SEPA
    public function getMandates(): array;
}
```

---

### UpplerAddressService

Gestion des adresses.

```php
// src/Service/UpplerAddressService.php
class UpplerAddressService extends AbstractUpplerService
{
    // Liste des adresses du buyer
    public function getAddresses(): array;
    
    // Créer une adresse
    public function createAddress(array $data): array;
    
    // Mettre à jour une adresse
    public function updateAddress(int $addressId, array $data): array;
    
    // Supprimer une adresse
    public function deleteAddress(int $addressId): bool;
}
```

## 🔧 AbstractUpplerService - La classe de base

### Méthode principale : `request()`

```php
public function request(
    string $method,              // GET, POST, PUT, PATCH, DELETE
    string $path,                // Chemin de l'API (ex: 'v1/buyer/product/123')
    array $options = [],         // Options Symfony HttpClient
    bool $isAdmin = false,       // Utiliser le token Admin
    bool $withoutToken = false,  // Sans authentification
    bool $withCache = false,     // Activer le cache HTTP
    bool $addCustomLog = false,  // Logger la requête en BDD
): bool|ResponseInterface;
```

### Gestion automatique des tokens

```php
// Mode User (par défaut) - Token en session
$response = $this->request('GET', 'v1/buyer/cart');

// Mode Admin - Token Admin depuis fichier
$response = $this->request('GET', 'v1/administrator/product/123', isAdmin: true);

// Sans token - Endpoints publics
$response = $this->request('POST', 'oauth/token', withoutToken: true);
```

### Retry automatique sur 401

Si l'API retourne un `401 Unauthorized`, le service :
1. Renouvelle automatiquement le token
2. Réessaye la requête
3. Lève une exception si ça échoue à nouveau

## 📡 Endpoints Uppler couramment utilisés

### Produits

| Endpoint | Description |
|----------|-------------|
| `GET v1/buyer/product/{id}` | Détail d'un produit |
| `POST v1/buyer/search/product` | Recherche de produits |
| `GET v1/buyer/variant/{id}` | Détail d'un variant |
| `GET v1/buyer/category` | Liste des catégories |

### Panier

| Endpoint | Description |
|----------|-------------|
| `GET v1/buyer/cart` | Panier actuel |
| `POST v1/buyer/cart/{id}/items` | Ajouter au panier |
| `PATCH v1/buyer/cart/item/{id}` | Modifier quantité |
| `DELETE v1/buyer/cart/item/{id}` | Supprimer du panier |

### Commandes

| Endpoint | Description |
|----------|-------------|
| `GET v1/buyer/order` | Liste des commandes |
| `GET v1/buyer/order/{id}` | Détail d'une commande |
| `POST v1/buyer/cart/{id}/checkout` | Valider le panier |

### Company

| Endpoint | Description |
|----------|-------------|
| `GET v1/buyer/company` | Infos de la company |
| `GET v1/buyer/address` | Liste des adresses |
| `POST v1/buyer/address` | Créer une adresse |

## 🧪 Mocker l'API Uppler dans les tests

Les mocks sont dans `tests/Resources/` :

```php
// tests/MockClientCallback.php
class MockClientCallback
{
    public function __invoke(string $method, string $url, array $options): ResponseInterface
    {
        // Retourne des réponses mockées selon l'URL
        return match(true) {
            str_contains($url, '/buyer/cart') => $this->mockCart(),
            str_contains($url, '/buyer/product') => $this->mockProduct(),
            default => throw new \Exception("URL non mockée: $url"),
        };
    }
}
```

## ⚠️ Points d'attention

1. **Rate limiting** : L'API Uppler a des limites de requêtes
2. **Tokens expirés** : Les tokens ont une durée de vie limitée
3. **Données en session** : Le token user est en session PHP, attention au scaling
4. **Logs** : Utilisez `addCustomLog: true` pour débugger les problèmes API
5. **Cache** : Activez le cache pour les données qui changent peu (catégories...)

