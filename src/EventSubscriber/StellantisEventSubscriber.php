<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Events\UserAcceptStellantisModalEvent;
use App\Service\StellantisService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class StellantisEventSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private LoggerInterface $logger,
        private StellantisService $stellantisService,
        private EntityManagerInterface $em,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            UserAcceptStellantisModalEvent::class => 'onAcceptStellantis',
        ];
    }

    public function onAcceptStellantis(UserAcceptStellantisModalEvent $event): void
    {
        try {
            $adherent = $event->getAccount()->getAdherent();
            $adherent->setStellantisModalValidated(true);
            $this->em->persist($adherent);
            $this->em->flush();

            $this->stellantisService->processStellantisSubscription($event->getAccount());
        } catch (\Exception $e) {
            $this->logger->error('Error in Stellantis event subscriber: '.$e->getMessage());
            throw $e;
        }
    }
}
