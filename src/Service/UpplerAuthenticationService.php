<?php

declare(strict_types=1);

namespace App\Service;

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
    public function authenticateUser(string $username, string $password, User $user): bool
    {
        $session = $this->requestStack->getSession();
        $session->start();
        $session->clear();

        if (empty($user->getUpplerClientId()) || empty($user->getUpplerClientSecret())) {
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
                        "username" => $username,
                        "password" => $password
                    ]
                ],
                true
            );
            // la réponse Uppler est OK on récupère les infos du tokenUser
            if ($res && Response::HTTP_OK ===$res->getStatusCode()) {
                $payload = json_decode($res->getContent());
                $tokenDatas = json_decode(
                    base64_decode(
                        str_replace(
                            '_',
                            '/',
                            str_replace(
                                '-',
                                '+',
                                explode('.', $payload->token)[1]
                            )
                        )
                    )
                );
                $user->setUpplerUserId($tokenDatas->userId);
                $user->setCompanyId($tokenDatas->companyId);
                $user->setCompanyName($tokenDatas->company);
                // grace aux paramétres de connexion on sollicite un accessToken pour ce user
                $this->getUserToken($payload->client_id, $payload->client_secret);
                //si l'accessToken a été récupéré il doit être en session,
                // si c'est le cas on stocke  aussi les infos du tokenUser en session
                // et les client_id/client_secret en bdd pour les réutiliser à chaque connexion
                if ($session->has('access_token') && !empty($session->get('access_token'))) {
                    $user->setUpplerClientId($payload->client_id);
                    $user->setUpplerClientSecret($payload->client_secret);
                    $this->em->persist($user);
                    $this->em->flush();
                    $this->updateUser($user);
                    $session->set('token_datas', $tokenDatas);
                    return true;
                }
            }
        } else {
            // grace aux paramétres de connexion déjà connus on sollicite un accessToken pour ce user
            $this->getUserToken($user->getUpplerClientId(), $user->getUpplerClientSecret());
            $this->updateUser($user);
            //si l'accessToken a été récupéré il doit être en session,
            // si c'est le cas on stocke  aussi les infos du tokenUser
            if ($session->has('access_token') && !empty($session->get('access_token'))) {
                return true;
            }
        }

        return false;
    }

    // met à jour les données d'un user en db à partir de ses données Uppler
    private function updateUser(User $user)
    {
        $session = $this->requestStack->getSession();
        $session->start();
        //on demande l'obtention d'un token Admin Uppler
        $this->getAdminToken();

        if (null === $this->adminToken) {
            return false;
        }

        if (!$session->has('access_token') || empty($session->get('access_token'))) {
            return false;
        }

        if ($user->isMaster()) {
            $res = $this->request(
                'GET',
                $this->apiUrl . 'v1/administrator/buyer/' . $user->getCompanyId(),
                [],
                true
            );
        } else {

        }

        if (Response::HTTP_OK === $res->getStatusCode()) {
            $payload = json_decode($res->getContent());
            if (property_exists($payload, 'master_user')) {
                $user->setEmail($payload->master_user->email);
                $user->setFirstName($payload->master_user->firstname);
                $user->setLastName($payload->master_user->lastname);
                $user->setLastLogin(new \DateTime('now'));
                $user->setCreatedAt(new \DateTime($payload->master_user->created_at));
                $user->setUpdatedAt(new \DateTime($payload->master_user->updated_at));
                $this->em->persist($user);
                $this->em->flush();
                return true;
            }
        }

    }
}
