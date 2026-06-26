# 03 - Modèle de données (Entités)

## 📊 Diagramme des relations principales

```
┌─────────────────┐
│     Channel     │──────────────┐
│  (Canal/Tenant) │              │
└────────┬────────┘              │
         │ 1                     │ 1
         │                       │
         ▼ *                     ▼ 1
┌─────────────────┐      ┌────────────────────┐
│    Adherent     │      │  ChannelParameter  │
│ (Entreprise Neo)│      │  (Design, options) │
└────────┬────────┘      └────────────────────┘
         │ 1
         │
         ▼ *
┌─────────────────┐       ┌─────────────────┐
│     Account     │──────▶│      User       │
│ (Compte Uppler) │ *   1 │ (Utilisateur)   │
└────────┬────────┘       └─────────────────┘
         │
         │
         ▼
┌─────────────────┐
│  API Uppler     │
│  (CompanyID,    │
│   UserID...)    │
└─────────────────┘
```

## 🏢 Entités principales

### User

L'utilisateur de l'application avec authentification JWT.

```php
// src/Entity/User.php
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    private ?Uuid $id;
    private ?string $email;
    private ?string $username;        // Identifiant unique de connexion
    private ?string $firstName;
    private ?string $lastName;
    private array $roles = [];
    private ?string $password;
    private ?bool $enabled;
    private ?\DateTimeInterface $lastLogin;
    private Collection $accounts;     // Relation OneToMany vers Account
}
```

**Points clés :**
- Un User peut avoir **plusieurs Accounts** (multi-compte)
- Le `username` est unique et sert à la connexion
- Le `currentAccount` est l'account actuellement sélectionné (non persisté)

---

### Account

Liaison entre un User local et un compte sur l'API Uppler.

```php
// src/Entity/Account.php
class Account
{
    private ?Uuid $id;
    private ?int $upplerUserId;       // ID User côté Uppler
    private ?int $upplerSubAccountId; // ID SubAccount côté Uppler
    private ?int $upplerCompanyId;    // ID Company côté Uppler
    private ?string $upplerUsername;
    private ?string $upplerPassword;
    private ?string $upplerClientId;     // OAuth credentials Uppler
    private ?string $upplerClientSecret;
    private ?User $user;              // Relation ManyToOne
    private ?Adherent $adherent;      // Relation ManyToOne
    private ?bool $enabled;
    private ?\DateTimeInterface $lastConnexion;
}
```

**Points clés :**
- Stocke les credentials OAuth pour l'API Uppler
- Lié à un **Adherent** (entreprise)
- Permet le multi-compte par User

---

### Channel

Représente un canal/tenant de l'application (multi-marque).

```php
// src/Entity/Channel.php
class Channel
{
    // Codes des channels disponibles
    public const ARTEMA = 'ARTEMA';
    public const DLR = 'DLR';
    public const QANTIS_ACHAT = 'QANTIS_ACHAT';
    // ... autres channels

    private ?Uuid $id;
    private ?string $name;         // "QANTIS Marketplace"
    private ?string $code;         // "QANTIS_ACHAT" (unique)
    private ?string $hostname;     // "marketplace.qantis.co"
    private ?ChannelParameter $channelParameter;
    private Collection $channelOptions;
    private Collection $adherents;
}
```

**Points clés :**
- Le `code` est unique et utilisé dans le header `X-channel`
- Le `hostname` permet d'identifier le channel par l'URL
- Configuration dans `config/channels.yaml`

---

### Adherent

Entreprise adhérente à un channel (provient du système Neo/Sugar).

```php
// src/Entity/Adherent.php
class Adherent
{
    private ?Uuid $id;           // ID provenant de Neo/Sugar (non auto-généré)
    private ?string $name;
    private ?string $siret;
    private ?string $street;
    private ?string $city;
    private ?string $postalcode;
    private ?string $hashkey;    // Clé pour l'auto-login
    private ?string $logo;
    private ?Channel $channel;
    private ?self $parent;       // Adhérent parent (hiérarchie)
    private Collection $children;
    private Collection $accounts;
    private Collection $accordStatuts;
}
```

**Points clés :**
- Les IDs viennent du système externe (Neo/Sugar)
- Possède une `hashkey` pour l'authentification auto-login
- Peut avoir des adhérents enfants (structure hiérarchique)

---

### Accord & AccordStatut

Accords commerciaux entre adhérents et partenaires.

```php
// src/Entity/Accord.php
class Accord
{
    private ?Uuid $id;
    private ?Partner $partner;
    private ?string $name;
    private ?string $logo;
    private bool $hasStore = false;
    private Collection $stores;
}

// src/Entity/AccordStatut.php
class AccordStatut
{
    private ?Uuid $id;
    private ?Adherent $adherent;
    private ?Accord $accord;
    private bool $isSubscribed = false;  // Adhérent inscrit à l'accord
}
```

---

### Partner & PartnerStore

Partenaires commerciaux et leurs magasins.

```php
// src/Entity/Partner.php
class Partner
{
    private ?Uuid $id;
    private ?string $name;
    private ?int $upplerSellerId;  // ID vendeur Uppler
}

// src/Entity/PartnerStore.php
class PartnerStore
{
    private ?Uuid $id;
    private ?Partner $partner;
    private ?string $name;
    private ?string $address;
    private ?float $latitude;
    private ?float $longitude;
}
```

---

### Autres entités utiles

| Entité | Description |
|--------|-------------|
| `ChannelParameter` | Design du channel (logo, couleurs, documents légaux) |
| `ChannelOption` | Options activées pour un channel (features flags) |
| `Favorite` | Liste de favoris d'un utilisateur |
| `FavoriteProduct` | Produit dans une liste de favoris |
| `SavedCart` | Panier sauvegardé |
| `SavedCartProduct` | Produit dans un panier sauvegardé |
| `CartSavings` | Économies calculées sur un panier |
| `Setting` | Paramètres globaux de l'application |
| `LogAccountConnection` | Logs de connexion des comptes |
| `LogAutoLoginError` | Logs des erreurs d'auto-login |

## 🔗 Relations clés à retenir

```
User (1) ──────────────── (*) Account
Account (*) ────────────── (1) Adherent  
Adherent (*) ───────────── (1) Channel
Channel (1) ────────────── (1) ChannelParameter
Channel (1) ────────────── (*) ChannelOption
Adherent (1) ───────────── (*) AccordStatut
AccordStatut (*) ────────── (1) Accord
Accord (*) ─────────────── (1) Partner
Partner (1) ────────────── (*) PartnerStore
```

