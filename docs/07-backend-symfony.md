# 07 - Backend Symfony & API Platform

## 🏗️ Architecture Backend

### Stack technique

| Technologie | Version | Usage |
|-------------|---------|-------|
| PHP | 8.3+ | Langage |
| Symfony | 6.4 | Framework |
| API Platform | 4.x | API REST |
| Doctrine ORM | 3.x | ORM/Database |
| Lexik JWT | 2.x | Authentification JWT |
| Messenger | 6.x | Messages asynchrones |

## 📡 API Platform

### Configuration d'une ressource

```php
// src/Entity/Account.php
#[ApiResource(
    operations: [
        new Get(
            normalizationContext: ['groups' => ['account:get']],
            security: 'is_granted("ROLE_API") or object.getUser() == user'
        ),
        new GetCollection(
            normalizationContext: ['groups' => ['user:simple', 'account:get']],
            provider: AccountProvider::class
        ),
    ]
)]
#[ORM\Entity(repositoryClass: AccountRepository::class)]
class Account
{
    // ...
}
```

### State Providers

Personnalisent la récupération des données :

```php
// src/State/Provider/AccountProvider.php
class AccountProvider implements ProviderInterface
{
    public function __construct(
        private Security $security,
        private AccountRepository $accountRepository,
    ) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $user = $this->security->getUser();
        
        // Retourne uniquement les comptes de l'utilisateur connecté
        return $this->accountRepository->findBy([
            'user' => $user,
            'enabled' => true,
        ]);
    }
}
```

### State Processors

Personnalisent la persistance des données :

```php
// src/State/Processor/AdherentPersistProcessor.php
class AdherentPersistProcessor implements ProcessorInterface
{
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): Adherent
    {
        // Logique métier avant persistance
        $data->setUpdatedAt(new \DateTimeImmutable());
        
        $this->entityManager->persist($data);
        $this->entityManager->flush();
        
        return $data;
    }
}
```

### Groupes de sérialisation

```php
#[ORM\Column]
#[Groups(['user:simple', 'account:get'])]  // Inclus dans ces groupes
private ?string $username = null;

#[ORM\Column]
#[Groups(['account:get'])]  // Uniquement dans account:get
private ?int $upplerUserId = null;

#[ORM\Column]
// Pas de Groups = jamais exposé en API
private ?string $upplerPassword = null;
```

## 🎮 Controllers

### Controller API personnalisé

```php
// src/Controller/Api/ContactController.php
#[Route('/api/contact')]
class ContactController extends AbstractController
{
    public function __construct(
        private RequestContactMailerService $mailerService,
    ) {}

    #[Route('', methods: ['POST'])]
    public function contact(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        $this->mailerService->sendContactEmail($data);
        
        return new JsonResponse(['success' => true]);
    }
}
```

### Controller avec Channel

```php
// src/Controller/LoginController.php
class LoginController extends AbstractController implements ChannelAwareControllerInterface
{
    use ChannelAwareControllerTrait;

    #[Route('/login/reset-password', name: 'reset_password')]
    public function request(Request $request): Response
    {
        $channel = $this->getChannel($request);  // Récupère le channel depuis la requête
        
        // ...
    }
}
```

### Trait ChannelAware

```php
// src/Controller/ChannelAwareControllerTrait.php
trait ChannelAwareControllerTrait
{
    protected function getChannel(Request $request): Channel
    {
        return $request->attributes->get('channel');
    }
}
```

## 🔧 Services

### Convention de nommage

- Services métier : `src/Service/`
- Nommage : `{Domain}Service.php`

### Injection de dépendances

```php
// src/Service/CartService.php
class CartService
{
    public function __construct(
        private UpplerCartService $upplerCartService,
        private EntityManagerInterface $em,
        private Security $security,
    ) {}

    public function calculateSavings(Cart $cart): CartSavings
    {
        // ...
    }
}
```

### Service Uppler typique

```php
// src/Service/UpplerOrderService.php
class UpplerOrderService extends AbstractUpplerService
{
    public function getOrders(int $page = 1, int $perPage = 10): array
    {
        $response = $this->request(
            'GET',
            sprintf('v1/buyer/order?page=%d&perPage=%d', $page, $perPage)
        );

        if (!$response || $response->getStatusCode() !== Response::HTTP_OK) {
            throw new BadRequestHttpException('Failed to fetch orders');
        }

        return json_decode($response->getContent(), true);
    }
}
```

## 📬 Symfony Messenger

### Messages

```php
// src/Message/SendEmailNotification.php
class SendEmailNotification
{
    public function __construct(
        public readonly string $email,
        public readonly string $subject,
        public readonly string $template,
        public readonly array $context = [],
    ) {}
}
```

### Handlers

```php
// src/MessageHandler/SendEmailNotificationHandler.php
#[AsMessageHandler]
class SendEmailNotificationHandler
{
    public function __construct(
        private MailerInterface $mailer,
        private Environment $twig,
    ) {}

    public function __invoke(SendEmailNotification $message): void
    {
        $html = $this->twig->render($message->template, $message->context);
        
        $email = (new Email())
            ->to($message->email)
            ->subject($message->subject)
            ->html($html);
            
        $this->mailer->send($email);
    }
}
```

## 🎯 Event Subscribers

```php
// src/EventSubscriber/FirstConnexionSubscriber.php
#[AsEventListener(event: FirstConnexionEvent::class)]
class FirstConnexionSubscriber
{
    public function __construct(
        private MessageBusInterface $bus,
    ) {}

    public function __invoke(FirstConnexionEvent $event): void
    {
        $user = $event->getUser();
        $channel = $event->getChannel();
        
        $this->bus->dispatch(new SendEmailNotification(
            email: $user->getEmail(),
            subject: 'Bienvenue sur ' . $channel->getName(),
            template: 'emails/first_connexion.html.twig',
            context: ['user' => $user, 'channel' => $channel],
        ));
    }
}
```

## 🗄️ Repositories

```php
// src/Repository/UserRepository.php
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function findUserByUsernameOrEmail(string $identifier): ?User
    {
        return $this->createQueryBuilder('u')
            ->where('u.username = :identifier')
            ->orWhere('u.email = :identifier')
            ->setParameter('identifier', $identifier)
            ->getQuery()
            ->getOneOrNullResult();
    }
    
    public function findActiveUsersByChannel(Channel $channel): array
    {
        return $this->createQueryBuilder('u')
            ->join('u.accounts', 'a')
            ->join('a.adherent', 'ad')
            ->where('ad.channel = :channel')
            ->andWhere('u.enabled = true')
            ->setParameter('channel', $channel)
            ->getQuery()
            ->getResult();
    }
}
```

## 📝 DTOs (Data Transfer Objects)

```php
// src/Dto/AccordView.php
class AccordView
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly ?string $logo,
        public readonly bool $hasStore,
        public readonly array $stores = [],
    ) {}
}
```

## ⚙️ Configuration des services

```yaml
# config/services.yaml
services:
  _defaults:
    autowire: true
    autoconfigure: true

  App\:
    resource: '../src/'
    exclude: '../src/{DependencyInjection,Entity,Kernel.php}'

  # Service avec arguments spécifiques
  App\Service\AbstractUpplerService:
    arguments:
      $upplerEnv: '%uppler_env%'
      $adminClientId: '%uppler_admin_client_id%'
      $adminClientSecret: '%uppler_admin_client_secret%'
      $adminTokenFile: '%uppler_admin_token_file%'
```

## 🔒 Conventions de code

### PSR-12 + Typage strict

```php
<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class UserService
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {}

    public function findByEmail(string $email): ?User
    {
        return $this->em->getRepository(User::class)
            ->findOneBy(['email' => $email]);
    }
}
```

### Attributs PHP 8

```php
// Routes
#[Route('/api/users', name: 'api_users_')]
class UserController {}

// Entités
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
class User {}

// Validation
#[Assert\NotBlank]
#[Assert\Email]
private ?string $email = null;
```

