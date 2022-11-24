<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Service\Attribute\Required;

class UpplerAuthenticationService extends HttpClientProvider
{
    #[Required]
    public RequestStack $requestStack;

    // Permet d'authentifier un user auprés d'Uppler
    public function authenticateUser(string $username, string $password): bool
    {
        //on demande l'obtention d'un token Admin Uppler
        $this->getAdminToken();

        if (null === $this->adminToken) {
            return false;
        }

        $session = $this->requestStack->getSession();
        $session->start();
        $session->clear();

        // on sollicite des paramétres de connexion pour ce suer auprés d'Uppler
        // si le username ou le password sont faux on le sait ici
        $res = $this->request(
            'POST',
            $this->apiUrl . 'v1/user/access-token',
            [
                "auth_basic" => ["quantis","jj0tFWJulNYjDc"],
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

            // grace aux paramétres de connexion on sollicite un accessToken pour ce user
            $this->getUserToken($payload->client_id, $payload->client_secret);

            //si l'accessToken a été récupéré il doit être en session,
            // si c'est le cas on stocke  aussi les infos du tokenUser
            if ($session->has('access_token') && !empty($session->get('access_token'))) {
                $session->set('token_datas', $tokenDatas);
                return true;
            }
        }

        return false;
    }

}
