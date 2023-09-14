<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Account;
use App\Entity\LogAccountConnection;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Service\Attribute\Required;

class UpplerAuthenticationService extends AbstractUpplerService
{
    #[Required]
    public EntityManagerInterface $em;

    // Permet d'authentifier un user auprés d'Uppler
    public function authenticateUser(Account $account): bool
    {
        $session = $this->requestStack->getSession();

        $session->clear();

        if (empty($account->getUpplerClientId()) || empty($account->getUpplerClientSecret())) {
            return false;
        } else {
            // grace aux paramétres de connexion déjà connus on sollicite un accessToken pour ce user
            $this->getUserToken($account);

            // si l'accessToken a été récupéré il doit être en session,
            // si c'est le cas on stocke  aussi les infos du tokenUser
            if ($session->has('access_token') && !empty($session->get('access_token'))) {
                $account->setLastConnexion(new DateTime('now'));
                $this->em->persist($account);

                $log = new LogAccountConnection();
                $log->setAccount($account);
                $log->setConnectedAt(new \DateTimeImmutable('now'));
                $this->em->persist($log);

                $this->em->flush();

                return true;
            }
        }

        return false;
    }
}
