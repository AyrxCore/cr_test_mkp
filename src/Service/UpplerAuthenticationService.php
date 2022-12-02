<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Account;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Service\Attribute\Required;

class UpplerAuthenticationService extends HttpClientProvider
{
    #[Required]
    public RequestStack $requestStack;

    #[Required]
    public EntityManagerInterface $em;

    // Permet d'authentifier un user auprés d'Uppler
    public function authenticateUser(Account $account): bool
    {
        $session = $this->requestStack->getSession();
        $session->start();
        $session->clear();

        if (empty($account->getUpplerClientId()) || empty($account->getUpplerClientSecret())) {
            //on demande l'obtention d'un token Admin Uppler
            $this->getAdminToken();

            if (null === $this->adminToken) {
                return false;
            }

            // on sollicite des paramétres de connexion pour ce suer auprés d'Uppler
            // si le username ou le password sont faux on le sait ici
            $res = $this->request(
                'POST',
                $this->apiUrl . 'v1/user/access-token',
                [
                    "body" => [
                        "username" => $account->getUpplerUsername(),
                        "password" => $account->getUpplerPassword()
                    ]
                ],
                true
            );
            // la réponse Uppler est OK on récupère les infos du tokenUser
            if ($res && Response::HTTP_OK ===$res->getStatusCode()) {
                $payload = json_decode($res->getContent());
                $account->setUpplerClientId($payload->client_id);
                $account->setUpplerClientSecret($payload->client_secret);
                // grace aux paramétres de connexion on sollicite un accessToken pour ce user
                $this->getUserToken($account);
                //si l'accessToken a été récupéré il doit être en session,
                // si c'est le cas on stocke  aussi les infos du tokenUser en session
                // et les client_id/client_secret en bdd pour les réutiliser à chaque connexion
                if ($session->has('access_token') && !empty($session->get('access_token'))) {
                    $account->setLastConnexion(new \DateTime('now'));
                    $this->em->persist($account);
                    $this->em->flush();
                    return true;
                }
            }
        } else {
            // grace aux paramétres de connexion déjà connus on sollicite un accessToken pour ce user
            $this->getUserToken($account);

            //si l'accessToken a été récupéré il doit être en session,
            // si c'est le cas on stocke  aussi les infos du tokenUser
            if ($session->has('access_token') && !empty($session->get('access_token'))) {
                $account->setLastConnexion(new \DateTime('now'));
                $this->em->persist($account);
                $this->em->flush();
                return true;
            }
        }

        return false;
    }

    public function getUserBuyerDatas(): object | null
    {
        $session = $this->requestStack->getSession();
        /**@var Account $account*/
        $account = $session->get('account');
        $res = $this->request(
            'GET',
            $this->apiUrl . 'v1/buyer/profile/' . $account->getUpplerCompanyId() . '?expand[]=address'
        );
        if (Response::HTTP_OK === $res->getStatusCode()) {

           return json_decode($res->getContent());
        }

        return null;
    }

}
