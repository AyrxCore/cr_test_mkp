<?php

declare(strict_types=1);

namespace App\Service;

use App\Enum\UpplerWebhookNotificationTypes;
use App\Exception\NotHandledUpplerWebhookNotificationException;
use App\Message\UpplerOrderUpdateNotificationMessage;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\MessageBusInterface;

class UpplerWebhookService
{
    public function __construct(
        #[Autowire('%uppler_webhook_secret%')]
        private string $upplerWebhookSecret,
        private MessageBusInterface $messageBus,
    ) {
    }

    public function isRequestAuthenticated(Request $request): bool
    {
        $signature = $request->headers->get('x-uppler-signature');

        return \hash_hmac('sha1', $request->getContent(), $this->upplerWebhookSecret) === $signature;
    }

    public function handleNotification(Request $request): void
    {
        $content = $request->request->all();

        if (!$resourceId = (int) $content['resource-id']) {
            throw new \InvalidArgumentException('Resource ID is required in the notification content.');
        }

        match ($content['event-type']) {
            UpplerWebhookNotificationTypes::ORDER_STATE_UPDATE->value => $this->messageBus->dispatch(new UpplerOrderUpdateNotificationMessage($resourceId)),
            default => throw new NotHandledUpplerWebhookNotificationException('Unsupported notification type: '.$content['event-type']),
        };
    }
}
