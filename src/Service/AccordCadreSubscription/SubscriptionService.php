<?php

declare(strict_types=1);

namespace App\Service\AccordCadreSubscription;

use App\Dto\AccountAccordCadre;
use App\Entity\AccordStatut;
use App\Entity\Account;
use App\Entity\LogAccordStatutRequest;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Uid\Uuid;

class SubscriptionService
{
    public function __construct(
        private EntityManagerInterface $em,
        private LoggerInterface $logger,
    ) {
    }

    /**
     * @throws \Exception
     */
    public function subscription(
        string $accordId,
        Account $account,
    ): bool {
        try {
            return $this->processAccordStatus($account, $accordId);
        } catch (\Exception $exception) {
            $this->logger->critical(
                "Erreur d'envoi de demande de subscription "
                .$account->getUser()->getEmail().' '.$account->getAdherent()->getName().' : '.
                $exception->getMessage()
            );

            return false;
        }
    }

    /**
     * @throws \Exception
     */
    private function processAccordStatus(Account $account, string $accordId): bool
    {
        $created = false;
        try {
            $accordStatut = $this->em->getRepository(AccordStatut::class)->findOneBy([
                'adherent' => $account->getAdherent()->getId(),
                'accordId' => $accordId,
            ]);

            if (!$accordStatut) {
                $accordStatut = new AccordStatut();
                $accordStatut->setAdherent($account->getAdherent());
                $accordStatut->setAccordId(new Uuid($accordId));
                $accordStatut->setAccordStatutRequestAt(new \DateTime('now'));
                $accordStatut->setStatus(AccountAccordCadre::PROCESS_STATUS_PENDING);

                $log = new LogAccordStatutRequest();
                $log->setAccordId(new Uuid($accordId));
                $log->setAccount($account);
                $log->setCreatedAt(new \DateTimeImmutable('now'));
                $this->em->persist($log);
                $created = true;
            } else {
                if ($accordStatut->getStatus() === AccountAccordCadre::PROCESS_STATUS_NOT_ACTIVATED) {
                    $accordStatut->setStatus(AccountAccordCadre::PROCESS_STATUS_PENDING);
                    $created = true;
                }
            }

            $this->em->persist($accordStatut);
            $this->em->flush();

            return $created;
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
