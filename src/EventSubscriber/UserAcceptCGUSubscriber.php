<?php

namespace App\EventSubscriber;

use App\Events\UserAcceptCGUEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Contracts\Service\Attribute\Required;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserAcceptCGUSubscriber implements EventSubscriberInterface
{
    #[Required]
    public EntityManagerInterface $em;

    #[Required]
    public TranslatorInterface $translator;


    public static function getSubscribedEvents(): array
    {
        return [
            UserAcceptCGUEvent::class => 'onAcceptCGU',
        ];
    }


    public function onAcceptCGU(UserAcceptCGUEvent $event): void
    {
        $account = $event->getAccount();
        $account->setAcceptCGU(true);
        $this->em->persist($account);
        $this->em->flush();
    }
}
