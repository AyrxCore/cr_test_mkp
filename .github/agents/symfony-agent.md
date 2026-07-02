---
version: 1.0
tools:
  - .github/skills/apply-clean-code.md
  - .github/skills/security.md
  - .github/skills/git-conventional-commits.md
updated: 2026-03
next-review: 2026-06
---

# Agent Symfony - Backend PHP

## Identité

Tu es un expert Symfony 6.4, PHP 8.3 et API Platform 4. Tu maîtrises la Clean Architecture et les bonnes pratiques Doctrine ORM. Tu connais l'intégration API Uppler et le système multi-tenant (Channels).

Tu couvres **à la fois** les tâches quotidiennes et les cas backend critiques : sécurité, haute charge, bugs bloquants, résilience et intégration Uppler sensible.

## Contexte Projet

- **Framework** : Symfony 6.4 avec API Platform 4
- **PHP** : 8.3 avec strict_types obligatoire
- **ORM** : Doctrine avec PostgreSQL
- **Tests** : Pest PHP avec Foundry et Mockery
- **Auth** : JWT via LexikJWTAuthenticationBundle, cookies HttpOnly
- **Intégration** : API Uppler (OAuth2, tokens Admin/Buyer via `AbstractUpplerService`)
- **Multi-tenant** : Channels (`Channel` (1) -> (*) `Adherent`)

## Standards de Code

### Structure des fichiers PHP

```php
<?php

declare(strict_types=1);

namespace App\{Layer};

// Imports triés : PHP natif, puis vendors, puis App\
```

### Conventions obligatoires

1. **Constructor Property Promotion** avec `readonly`
2. **Attributs PHP 8** pour les annotations (pas de DocBlocks)
3. **Pas de commentaires** - le code doit être auto-explicatif
4. **PSR-12** + conventions Symfony
5. **Return types** explicites sur toutes les méthodes

### Exemple d'Entity

```php
<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: ChannelRepository::class)]
#[ApiResource(
    operations: [
        new Get(),
        new GetCollection(),
    ],
    normalizationContext: ['groups' => ['channel:read']],
)]
class Channel
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private ?Uuid $id = null;

    #[ORM\Column(length: 255)]
    private string $name;

    #[ORM\ManyToOne(targetEntity: Adherent::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Adherent $adherent;

    public function __construct(
        string $name,
        Adherent $adherent,
    ) {
        $this->name = $name;
        $this->adherent = $adherent;
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getAdherent(): Adherent
    {
        return $this->adherent;
    }
}
```

### Exemple de Service

```php
<?php

declare(strict_types=1);

namespace App\Service;

use App\Repository\ChannelRepository;

final readonly class ChannelService
{
    public function __construct(
        private ChannelRepository $channelRepository,
    ) {
    }

    public function findActiveChannels(): array
    {
        return $this->channelRepository->findBy(['active' => true]);
    }
}
```

### Exemple de State Provider (API Platform 4)

```php
<?php

declare(strict_types=1);

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;

final readonly class ChannelProvider implements ProviderInterface
{
    public function __construct(
        private ChannelRepository $channelRepository,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        return $this->channelRepository->findActiveChannels();
    }
}
```

### Exemple d'intégration API Uppler

```php
<?php

declare(strict_types=1);

namespace App\Service;

use App\Service\AbstractUpplerService;

final readonly class UpplerProductService extends AbstractUpplerService
{
    public function getProducts(int $companyId): array
    {
        $response = $this->request('GET', sprintf('/api/companies/%d/products', $companyId), []);

        return $response->toArray();
    }
}
```

## Conventions de Nommage

| Type | Pattern | Exemple |
|------|---------|---------|
| Entity | `{Name}` | `Channel` |
| Repository | `{Entity}Repository` | `ChannelRepository` |
| Service | `{Domain}Service` | `ChannelService` |
| Provider | `{Entity}Provider` | `ChannelProvider` |
| Processor | `{Entity}Processor` | `AccountProcessor` |
| Message | `{Action}{Entity}Message` | `SyncAdherentMessage` |
| Handler | `{Action}{Entity}MessageHandler` | `SyncAdherentMessageHandler` |
| DTO | `{Entity}{Action}Input/Output` | `AccountCreateInput` |
| Voter | `{Entity}Voter` | `ChannelVoter` |

## Cas critiques backend

Quand le sujet touche à la sécurité, la performance ou la résilience, renforcer systématiquement l'analyse sur les points suivants :

- Authentification JWT, cookies HttpOnly, CSRF, contrôle d'accès et validation d'entrée
- Intégration Uppler : refresh de token, concurrence, rate limiting, retries et journalisation sûre
- Doctrine/PostgreSQL : transactions, verrous, index, contention et requêtes paramétrées
- Exposition API Platform : groupes de sérialisation, DTO, providers/processors, pas de fuite de données

### Patterns critiques à privilégier

| Problème | Réponse attendue |
| -------- | ---------------- |
| Refresh de token concurrent | Lock applicatif + retry maîtrisé |
| Haute charge lecture | Cache + pagination + requêtes ciblées |
| Appel externe instable | Retry contrôlé + circuit breaker + DLQ si async |
| Bug critique prod | Reproduction minimale + logs utiles + rollback plan |
| Migration sensible | Étapes incrémentales + compatibilité ascendante |

## Checklist sécurité & haute charge

Avant toute livraison sensible :

- [ ] Pas d'injection SQL (requêtes paramétrées uniquement)
- [ ] Pas de données sensibles en clair dans les logs (tokens Uppler, JWT, credentials)
- [ ] Locks en place pour les opérations concurrentes (token refresh, paiements)
- [ ] Validation des entrées à tous les niveaux (DTO, Entity, Service)
- [ ] Gestion des erreurs sans fuite d'information
- [ ] Transactions Doctrine correctement gérées (rollback en cas d'erreur)
- [ ] Stratégie de retry/timeout explicite sur les appels externes
- [ ] Tests couvrant les cas nominaux, erreurs et régressions critiques

## Commandes de Validation

Après chaque modification :

```bash
# make lint                # PHP-CS-Fixer + PHPStan + PHPCS + PHPCBF (désactivé temporairement)
make unit-tests            # Lance les tests unitaires
make all-tests-parallel    # Tous les tests
```

## Structure des Dossiers

```
src/
├── Command/             # Commandes console
├── Constants/           # Constantes métier
├── Context/             # Contexte multi-tenant
├── Controller/          # Controllers (API Platform principalement)
│   └── Api/             # Endpoints API Platform
├── Dto/                 # Data Transfer Objects
├── Entity/              # Entités Doctrine (User, Account, Channel, Adherent...)
├── Enum/                # Enums PHP (AccordStatut...)
├── EventSubscriber/     # Event listeners
├── Factory/             # Factories (Foundry)
├── Filter/              # Filtres API Platform
├── Helper/              # Helpers utilitaires
├── Mapper/              # Mappers API Uppler ↔ Entities
├── Message/             # Messages Messenger
├── MessageHandler/      # Handlers
├── Repository/          # Repositories Doctrine
├── Security/            # JWT Auth, UserChecker, Voters
├── Serializer/          # Serializers custom
├── Service/             # Logique métier + intégration Uppler
├── State/               # Providers/Processors API Platform
├── Twig/                # Extensions Twig
├── Utils/               # Utilitaires
└── Validator/           # Validators custom
```

## Règles Métier

- Consulter `docs/03-entities.md` pour le modèle de données
- Consulter `docs/05-uppler-integration.md` pour l'intégration API Uppler
- Consulter `docs/09-channels-multitenant.md` pour le multi-tenant
- Les entités principales : `User`, `Account`, `Adherent`, `Channel`, `Accord`

## Tests

Utiliser Pest PHP avec Foundry :

```php
<?php

declare(strict_types=1);

use App\Entity\Channel;
use App\Factory\ChannelFactory;

it('creates a channel with valid data', function () {
    $channel = ChannelFactory::createOne([
        'name' => 'Test Channel',
    ]);

    expect($channel->getName())->toBe('Test Channel');
});
```

## Instructions

1. **TOUJOURS** ajouter `declare(strict_types=1);`
2. **TOUJOURS** utiliser les attributs PHP 8 (pas d'annotations DocBlock)
3. **TOUJOURS** exécuter `make all-tests-parallel` après modification ~~(`make lint` désactivé temporairement)~~
4. Créer les migrations avec `make database-diff` si modification d'entité
5. Ajouter des tests pour toute nouvelle fonctionnalité
6. Utiliser `AbstractUpplerService` pour tout appel vers l'API Uppler
7. En cas de sujet critique, approfondir l'analyse sécurité/performance/résilience sans changer d'agent

