<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Account;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class StellantisService
{
    public function __construct(
        private EntityManagerInterface $em,
        private AccordCadreSubscriptionService $subscriptionService,
        private LoggerInterface $logger,
        #[Autowire('%STELLANTIS_PARAMS%')]
        private array $stellantisParams,
    ) {
    }

    public function processStellantisSubscription(Account $account): void
    {
        $adherent = $account->getAdherent();
        try {
            $accordId = \reset($this->stellantisParams['ACCORDS_IDS']);
            if ($accordId === false) {
                throw new \Exception('Le tableau ACCORDS_IDS est vide.');
            }

            $params = [
                'accordId' => $accordId,
                'accordName' => 'Stellantis',
            ];

            $this->subscriptionService->subscription(
                $params,
                $account->getId()->__toString(),
                $adherent->getChannel()
            );

            $this->logger->info('Stellantis subscription processed for adherent: '.$adherent->getId());
        } catch (\Exception $e) {
            $this->logger->error('Error processing Stellantis subscription: '.$e->getMessage());
            throw $e;
        }
    }

    public function cancelStellantisSubscription(Account $account): void
    {
        $adherent = $account->getAdherent();
        $adherent
            ->setStellantisModalValidated(true);
        $this->em->persist($adherent);
        $this->em->flush();
    }
}
