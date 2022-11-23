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
        try {
            $res = $this->client->request($method, $url, $options);
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

    // obtient un token pour le user et le stocke en session
    public function getUserToken(string $clientId, string $cleintSecret): bool
    {
        $session = $this->requestStack->getSession();
        $session->start();

        $accessToken = $this->getToken($clientId, $cleintSecret);
        if (null !== $accessToken) {
            //on stocke les données du user en session
            // elles seront utilisées pour toutes les rquêtes vers Uppler durant cette session
            $session->set('access_token', $accessToken);
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
