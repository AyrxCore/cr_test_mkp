# Symfony Scheduler Pattern

Guide pour implémenter des tâches planifiées via **Symfony Scheduler + Messenger**.

---

## Architecture

**Fonctionnement** : Le Scheduler génère automatiquement les messages selon leurs expressions cron quand le worker consomme le transport `scheduler_default`. Chaque message généré est envoyé au transport `default` pour être traité par les workers Messenger existants.

```
supervisord (startup)
    ↓
messenger:consume default scheduler_default
    ↓
Transport scheduler_default génère les messages selon le Schedule
    ↓ (si condition cron vraie)
Message envoyé au transport DEFAULT
    ↓
Worker Messenger consomme et traite le message
    ↓
MessageHandler exécute la logique métier
```

**Avantage clé** : Tout fonctionne via les workers Messenger existants. Aucun processus supplémentaire à gérer.

---

## Étapes d'implémentation

### 1. Créer la classe Message

```php
// src/Message/Crm/SyncProspectsMessage.php
namespace App\Message\Crm;

final class SyncProspectsMessage
{
}
```

### 2. Extraire la logique métier dans un Service

```php
// src/Service/Crm/ProspectsSync Service.php
namespace App\Service\Crm;

class ProspectsSyncService
{
    public function sync(): array
    {
        // Logique métier
        return ['processed' => 10, 'failed' => 0];
    }
}
```

### 3. Créer le MessageHandler

```php
// src/MessageHandler/SyncProspectsMessageHandler.php
namespace App\MessageHandler;

use App\Message\Crm\SyncProspectsMessage;
use App\Service\Crm\ProspectsSyncService;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class SyncProspectsMessageHandler
{
    public function __construct(
        private readonly ProspectsSyncService $prospectsSyncService,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function __invoke(SyncProspectsMessage $message): void
    {
        $result = $this->prospectsSyncService->sync();
        $this->logger->info('Prospects sync completed.', $result);
    }
}
```

### 4. Créer la classe Schedule

```php
// src/Scheduler/CrmSchedule.php
namespace App\Scheduler;

use App\Message\Crm\SyncProspectsMessage;
use Symfony\Component\Scheduler\Attribute\AsSchedule;
use Symfony\Component\Scheduler\RecurringMessage;
use Symfony\Component\Scheduler\Schedule;
use Symfony\Component\Scheduler\ScheduleProviderInterface;

#[AsSchedule('default')]
final class CrmSchedule implements ScheduleProviderInterface
{
    public function __construct(
        #[\Symfony\Component\DependencyInjection\Attribute\Autowire('%env(APP_MODE)%')]
        private readonly string $appMode,
    ) {
    }

    public function getSchedule(): Schedule
    {
        $schedule = new Schedule();

        if ('prod' === $this->appMode) {
            // Exécuter chaque jour à 14h30
            $schedule->add(
                RecurringMessage::cron('30 14 * * *', new SyncProspectsMessage()),
            );
        }

        return $schedule;
    }
}
```

### 5. Enregistrer le routing dans Messenger

```yaml
# config/packages/messenger.yaml
framework:
  messenger:
    transports:
      default:
        dsn: '%env(MESSENGER_TRANSPORT_DSN)%'
      scheduler_default:
        dsn: 'scheduler://default'

    routing:
      'App\Message\Crm\SyncProspectsMessage': default
```

### 6. Mettre à jour run-messenger-consume.sh

```bash
# docker/php-fpm/supervisor/run-messenger-consume.sh
bin/console messenger:consume default scheduler_default -vv --limit=10 --time-limit=1800
```

---

## Expressions Cron courantes

```php
// Minuit chaque jour
RecurringMessage::cron('0 0 * * *', new Message()),

// 14h30 chaque jour
RecurringMessage::cron('30 14 * * *', new Message()),

// Chaque lundi à 09:00
RecurringMessage::cron('0 9 * * 1', new Message()),

// Tous les 15 minutes
RecurringMessage::every('15 minutes', new Message()),

// Chaque heure à la minute 45
RecurringMessage::every('45 minutes', new Message()),
```

**Format cron** : `minute hour day month weekday`

---

## Tests

Tester le **service métier** :

```php
// tests/Unit/Service/Crm/ProspectsSyncServiceTest.php
\it('syncs prospects successfully', function () {
    $service = new ProspectsSyncService(...);
    $result = $service->sync();
    \expect($result['failed'])->toBe(0);
});
```

Tester le **message handler** :

```php
\it('logs sync results', function () {
    $logger = Mockery::mock(LoggerInterface::class);
    $service = Mockery::mock(ProspectsSyncService::class);
    $service->shouldReceive('sync')->andReturn(['processed' => 5, 'failed' => 0]);
    $logger->shouldReceive('info')->with('Prospects sync completed.', ...)->once();

    $handler = new SyncProspectsMessageHandler($service, $logger);
    $handler(new SyncProspectsMessage());
});
```

**Ne pas tester** : Le Scheduler lui-même (responsabilité de Symfony).

---

## Production Checklist

- [ ] Service métier avec tests unitaires ✅
- [ ] MessageHandler avec tests ✅
- [ ] Schedule créée avec `#[AsSchedule]` ✅
- [ ] Expression cron validée ✅
- [ ] Condition `if ('prod' === $appEnv)` ou équivalent ✅
- [ ] Message routée dans `messenger.yaml` ✅
- [ ] Workers updated : `messenger:consume default scheduler_default` ✅
- [ ] Logs structurés (via `logger`) ✅
- [ ] Rebuild image Docker + tests complets ✅

---

## Ajouter une nouvelle tâche planifiée

Suivez les 6 étapes ci-dessus. Un checklist rapide :

1. ✅ Message créée dans `src/Message/{Domain}/`
2. ✅ Service métier créé dans `src/Service/{Domain}/` avec méthode `sync()` ou équivalent
3. ✅ MessageHandler créé dans `src/MessageHandler/` avec `#[AsMessageHandler]`
4. ✅ Schedule créée dans `src/Scheduler/` avec `#[AsSchedule('default')]`
5. ✅ Message routée dans `config/packages/messenger.yaml`
6. ✅ Workers consomment `default scheduler_default` dans `run-messenger-consume.sh`
7. ✅ Tests unitaires pour Service et Handler

---

## Exemple complet : Djust Orders Sync

Voir `docs/13-djust-cart-savings-sync.md` pour une implémentation en production.

**Classes clés** :
- `src/Scheduler/DjustSchedule.php` — Déclenche chaque nuit à minuit
- `src/Message/Djust/SyncDjustOrdersStateMessage.php` — Payload
- `src/MessageHandler/SyncDjustOrdersStateMessageHandler.php` — Handler
- `src/Service/Djust/DjustOrdersSyncService.php` — Logique métier

