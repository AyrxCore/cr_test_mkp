<?php

declare(strict_types=1);

namespace App\Service;

use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Component\HttpClient\Exception\ServerException;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\Service\Attribute\Required;

abstract class HttpClientProvider
{
    #[Required]
    public HttpClientInterface $client;

    #[Required]
    public LoggerInterface $apiLogger;

    public string $adminToken;

    public string $apiUrl;

    public string $adminClientId;

    public string $adminClientSecret;

    public string $adminTokenFile;

    public function __construct(
        string $apiUrl,
        string $adminClientId,
        string $adminClientSecret,
        string $adminTokenFile
    ) {
        $this->apiUrl = $apiUrl;
        $this->adminClientId = $adminClientId;
        $this->adminClientSecret = $adminClientSecret;
        $this->adminTokenFile = $adminTokenFile;
    }

    // Expose la requête Http Symonfy afin de centraliser tous les appels
    public function request(string $method, string $url, array $options = [], $isAdmin = false)
    {
        if ($isAdmin) {
            $url .= '?access_token=' . $this->adminToken;
        }

        try {
            $res = $this->client->request($method, $url, $options);
            if (Response::HTTP_UNAUTHORIZED === $res->getStatusCode()) {
                $errorDatas = json_decode($res->getContent(false));
                // le token a expiré
                if (!is_object($errorDatas->error) && 'invalid_grant' === $errorDatas->error) {
                    // il s'agit d'une requête admin donc on renégocie un token admin et onr elance la requete
                    if ($isAdmin) {
                        $this->apiLogger->warning("Admin Token " . $this->adminToken ." has expired , request for new");
                        $this->getAdminToken(true);
                        $this->request($method, $url, $options, true);
                        //il s'agit d'un token user, on renégocie un token user,
                        //on le remplace dans le hearder et on relance la requete
                    } else {
                        $session = $this->requestStack->getSession();
                        $session->start();
                        if ($session->has('user_client_id') && $session->has('user_client_secret')) {
                            $userUpplerCredentials = $this->getUserOauth2Credentials();
                            $accessToken = $this->getUserToken(
                                $userUpplerCredentials["client_secret"],
                                $userUpplerCredentials["user_client_id"]
                            );
                            if ($accessToken) {
                                // ici le remplacement du token user
                            }
                            $this->request($method, $url, $options);
                        } else {
                            $this->apiLogger->critical("client_id and/or client_secret missing, token cannot renewed");
                        }
                    }
                } else {
                    $this->apiLogger->critical("Error " . $res->getContent(false));
                }
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

    //obtient un token pour l'administrateur et le stocke dans
    // un fichier afin qu'il soit partagé entre toutes les sessions
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

    public function getUserOauth2Credentials(): array
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
    public function getUserToken(string $clientId, string $clientSecret): bool
    {
        $session = $this->requestStack->getSession();
        $session->start();

        $accessToken = $this->getToken($clientId, $clientSecret);
        if (null !== $accessToken) {
            //on stocke les données du user en session
            // elles seront utilisées pour toutes les rquêtes vers Uppler durant cette session
            $session->set('access_token', $accessToken);
            $session->set('user_client_id', $clientId);
            $session->set('user_client_secret', $clientSecret);
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
                "auth_basic" => ["quantis","jj0tFWJulNYjDc"],
                "body" => [
                    "grant_type" => "client_credentials",
                    "client_id" => $clientId,
                    "client_secret" => $clientSecret
                ]
            ]
        );

        if (Response::HTTP_OK === $res->getStatusCode()) {
            $datas = json_decode($res->getContent());
            return $datas;
        }

        return null;
    }

    public function getApiUrl()
    {
        return $this->apiUrl;
    }
}
