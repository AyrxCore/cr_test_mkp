<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Account;
use App\Entity\LogAccountConnection;
use Doctrine\ORM\EntityManagerInterface;

class LogAccountConnectionService
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    public function createLog(Account $account): void
    {
        $account->setLastConnexion(new \DateTime('now'));
        $this->em->persist($account);

        $log = new LogAccountConnection();
        $log->setAccount($account);
        $log->setConnectedAt(new \DateTimeImmutable('now'));
        $this->em->persist($log);

        $this->em->flush();
    }
}
