<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Account;
use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Component\HttpClient\Exception\ServerException;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Symfony\Contracts\Service\Attribute\Required;

abstract class HttpClientProvider
{
    #[Required]
    public HttpClientInterface $client;

    #[Required]
    public LoggerInterface $apiLogger;

    #[Required]
    public RequestStack $requestStack;

    private string $env;

    public string $adminToken;

    public string $apiUrl;

    public string $adminClientId;

    public string $adminClientSecret;

    public string $adminTokenFile;

    public function __construct(
        string $env,
        string $apiUrl,
        string $adminClientId,
        string $adminClientSecret,
        string $adminTokenFile
    ) {
        $this->env = $env;
        $this->apiUrl = $apiUrl;
        $this->adminClientId = $adminClientId;
        $this->adminClientSecret = $adminClientSecret;
        $this->adminTokenFile = $adminTokenFile;
    }

    // Expose la requête Http Symfony afin de centraliser tous les appels
    // isAdmin indique qu'il s'agit d'une requete en mode Admin et donc nécessité de passer le token Admin
    // wihtoutToken indique qu'il ne faut pas passer de token, uniquement valable pour le endpoint getAccessToken
    public function request(string $method, string $url, array $options = [], $isAdmin = false, $wihthoutToken = false)
    {
        $this->computeHeaders($url, $options, $isAdmin, $wihthoutToken);

        try {
            $res = $this->client->request($method, $url, $options);
            if (Response::HTTP_UNAUTHORIZED === $res->getStatusCode()) {
                $this->checkResponse($res, $method, $url, $options, $isAdmin);
            }
            return $res;
        } catch (ClientException $e) {
            $this->apiLogger->critical("Client Error " . $url . " : " .$e->getResponse()->getContent());
        } catch (ServerException $e) {
            $this->apiLogger->critical("Server Error " . $url . " : " .$e->getResponse()->getContent());
        } catch (\Exception $e) {
            $this->apiLogger->critical("Error " . $url . " : " .$e->getMessage());
        }
        return false;
    }

    // populate le bloc header de la requete
    private function computeHeaders(string &$url, array &$options = [], $isAdmin = false, $wihthoutToken = false): void
    {
        $session = $this->requestStack->getSession();
        // pas de token nécessaire et env de dév on ajoute juste le header http_basic
        if ('dev' === $this->env) {
            $options["auth_basic"] = ["quantis","jj0tFWJulNYjDc"];
        }

        if ($wihthoutToken) {
            return;
        }

        // récupère le token admin ou user
        $accessToken = $isAdmin ? $this->adminToken : $session->get('access_token')->access_token;

        if ('dev' === $this->env) {
            // ajoute token admin à l'url
            $url .=  (preg_match('/([^?&=#]+)=([^&#]*)/', $url)) ?  '&' : '?' ;
            $url .= 'access_token=' . $accessToken;
        } else {
            //ajoute le Bearer token user dans le header
            $options["headers"] = [
                'Authorization' => 'Bearer ' . $accessToken
            ];
        }
    }


    private function checkResponse(
        ResponseInterface $res,
        string $method,
        string $url,
        array $options,
        bool $isAdmin
    ): void {
        $errorDatas = json_decode($res->getContent(false));
        // le token a expiré
        if (!is_object($errorDatas->error) &&
            'invalid_grant' === $errorDatas->error
        ) {
            // il s'agit d'une requête admin donc on renégocie un token admin et on relance la requete
            if ($isAdmin) {
                $this->apiLogger->warning("Admin Token " . $this->adminToken ." has expired , request for new");
                $this->getAdminToken(true);
                $this->request($method, $url, $options, true);
            } else {
                // il s'agit d'un token user, on renégocie un token user,
                // on le remplace dans le hearder et on relance la requete
                $session = $this->requestStack->getSession();
                $session->start();
                if (!$session->has('user_client_id') &&
                    !$session->has('user_client_secret')
                ) {
                    throw  new \Exception("client_id and/or client_secret missing, token cannot renewed");
                }

                $userUpplerCredentials = $this->getUserOauth2Credentials();
                $accessToken = $this->getUserToken(
                    $userUpplerCredentials["client_secret"],
                    $userUpplerCredentials["user_client_id"]
                );
                // ici le remplacement du token user
                $options["headers"] = [
                    'Authorization' => 'Bearer ' . $accessToken
                ];
                $this->request($method, $url, $options);
            }
        } else {
            $this->apiLogger->critical("Error " . $res->getContent(false));
        }
    }

    //obtient un token pour l'administrateur et le stocke dans
    // un fichier afin qu'il soit partagé entre toutes les sessions
    // forceRefreh permet de forcer à refresh le token sans vérifier l'existant
    // (utile en cas de retour 401 'invalid_grant' d'Uppler)
    public function getAdminToken($forceRefresh = false): bool
    {
        $fileSystem = new Filesystem();
        if ($fileSystem->exists($this->adminTokenFile . 'token.txt') && !$forceRefresh) {
            $fp = fopen($this->adminTokenFile . 'token.txt', 'r');
            while (!feof($fp)) {
                $line = fgets($fp);
                $tab = explode(':', $line);
                if ('token' === $tab[0]) {
                    $token = trim($tab[1]);
                }
            }
        } else {
            $payload =  $this->getToken($this->adminClientId, $this->adminClientSecret);
            if (null !== $payload) {
                $fp = fopen($this->adminTokenFile . 'token.txt', 'w');
                fwrite(
                    $fp,
                    'token:' . $payload->access_token
                );
                $token = $payload->access_token;
            }
        }

        $this->adminToken = $token;

        return true;
    }

    // extrait de la session les credentials Oauth2 du user courant
    private function getUserOauth2Credentials(): array
    {
        $session = $this->requestStack->getSession();
        $session->start();

        if ($session->has('user_client_id') &&
            $session->has('user_client_secret')
        ) {
            return [
              'client_secret' => $session->get('user_client_secret'),
              'client_id' => $session->get('user_client_id')
            ];
        }

        return [];
    }

    // obtient un token pour le user et le stocke en session
    public function getUserToken(Account $account): bool
    {
        $session = $this->requestStack->getSession();
        $session->start();

        $accessToken = $this->getToken($account->getUpplerClientId(), $account->getUpplerClientSecret());
        if (null !== $accessToken) {
            //on stocke les données du user en session
            // elles seront utilisées pour toutes les requêtes vers Uppler durant cette session
            $session->set('access_token', $accessToken);
            $session->set('account', $account);
            return true;
        }
        return false;
    }


    //Obtient un accesssToken depuis l'API Uppler pour le user propriétaire des clientId/clientSecret
    public function getToken(string $clientId, string $clientSecret) : object | null
    {
        $res = $this->request(
            'POST',
            $this->apiUrl . 'oauth/v2/token',
            [
                "body" => [
                    "grant_type" => "client_credentials",
                    "client_id" => $clientId,
                    "client_secret" => $clientSecret
                ]
            ],
            false,
            true
        );

        if (Response::HTTP_OK === $res->getStatusCode()) {
            $datas = json_decode($res->getContent());
            return $datas;
        }

        return null;
    }

}
