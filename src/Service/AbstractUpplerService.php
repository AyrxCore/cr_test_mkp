<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Account;
use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpClient\CachingHttpClient;
use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Component\HttpClient\Exception\ServerException;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpCache\Store;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Symfony\Contracts\Service\Attribute\Required;

abstract class AbstractUpplerService
{
    // Liste des codes retours http considérés comme success
    private const HTTP_SUCCESS_RESPONSES = [
        Response::HTTP_OK,
        Response::HTTP_ACCEPTED,
        Response::HTTP_CREATED,
        Response::HTTP_NO_CONTENT,
        Response::HTTP_PARTIAL_CONTENT,
    ];

    #[Required]
    public LoggerInterface $apiLogger;

    private string $adminToken = '';

    public function __construct(
        private HttpClientInterface $upplerClient,
        protected RequestStack $requestStack,
        private string $upplerEnv,
        private string $adminClientId,
        private string $adminClientSecret,
        private string $adminTokenFile,
        private string $httpCachePath,
    ) {
    }

    // Expose la requête Http Symfony afin de centraliser tous les appels
    // isAdmin indique qu'il s'agit d'une requête en mode Admin et donc nécessité de passer le token Admin
    // withoutToken indique qu'il ne faut pas passer de token, uniquement valable pour le endpoint getAccessToken
    public function request(
        string $method,
        string $path,
        array $options = [],
        bool $isAdmin = false,
        bool $withoutToken = false,
        bool $withCache = false
    ): bool|ResponseInterface {
        if ($withCache) {
            $store = new Store($this->httpCachePath);
            $this->upplerClient = new CachingHttpClient($this->upplerClient, $store);
        }

        $origUrl = \getenv('UPPLER_API_URL').$path;
        $origOptions = $options;
        $this->computeHeaders($path, $options, $isAdmin, $withoutToken);

        try {
            $this->apiLogger->info('Token utilisé  '.$this->adminToken.' endpoint '.$path);
            $res = $this->upplerClient->request($method, $path, $options);

            if ($res->getStatusCode() === Response::HTTP_UNAUTHORIZED) {
                $this->apiLogger->critical('token '.$this->adminToken.' retour 401 ');
                $this->checkResponse($res, $method, $origUrl, $origOptions, $isAdmin);
                $this->computeHeaders($origUrl, $origOptions, $isAdmin, $withoutToken);
                $res = $this->upplerClient->request($method, $origUrl, $origOptions);
            } elseif (!\in_array($res->getStatusCode(), self::HTTP_SUCCESS_RESPONSES, true)) {
                $errorData = $res->getContent(false);
                $this->apiLogger->critical('error '.$errorData);
            }

            $this->apiLogger->info($res->getStatusCode().' requete OK url ==>  '.$path);

            return $res;
        } catch (ClientException $e) {
            $this->apiLogger->critical('Client Error '.$path.' : '.$e->getResponse()->getContent());
        } catch (ServerException $e) {
            $this->apiLogger->critical('Server Error '.$path.' : '.$e->getResponse()->getContent());
        } catch (\Exception $e) {
            $this->apiLogger->critical('Error '.$path.' : '.$e->getMessage());
        }

        return false;
    }

    // obtient un token pour l'administrateur et le stocke dans
    // un fichier afin qu'il soit partagé entre toutes les sessions
    // forceRefreh permet de forcer à refresh le token sans vérifier l'existant
    // (utile en cas de retour 401 'invalid_grant' d'Uppler)
    public function getAdminToken($forceRefresh = false): bool
    {
        $fileSystem = new Filesystem();
        if ($fileSystem->exists($this->adminTokenFile.'token.txt') && !$forceRefresh) {
            $fp = \fopen($this->adminTokenFile.'token.txt', 'r');
            while (!\feof($fp)) {
                $line = \fgets($fp);
                $tab = \explode(':', $line);
                if ($tab[0] === 'token') {
                    $token = \trim($tab[1]);
                }
            }
        } else {
            $payload = $this->getToken($this->adminClientId, $this->adminClientSecret);
            if ($payload !== null) {
                $fp = \fopen($this->adminTokenFile.'token.txt', 'w');
                \fwrite(
                    $fp,
                    'token:'.$payload->access_token
                );
                $token = $payload->access_token;
            }
        }

        $this->adminToken = $token;

        return true;
    }

    // obtient un token pour le user et le stocke en session
    public function getUserToken(Account $account): bool
    {
        $session = $this->requestStack->getSession();

        $accessToken = $this->getToken($account->getUpplerClientId(), $account->getUpplerClientSecret());
        if ($accessToken !== null) {
            // on stocke les données du user en session
            // elles seront utilisées pour toutes les requêtes vers Uppler durant cette session
            $session->set('access_token', $accessToken);
            $session->set('account', $account);

            return true;
        }

        return false;
    }

    // Obtient un accessToken depuis l'API Uppler pour le user propriétaire des clientId/clientSecret
    public function getToken(string $clientId, string $clientSecret): object|null
    {
        $res = $this->request(
            'POST',
            'oauth/v2/token',
            [
                'body' => [
                    'grant_type' => 'client_credentials',
                    'client_id' => $clientId,
                    'client_secret' => $clientSecret,
                ],
            ],
            withoutToken: true
        );

        if ($res && $res->getStatusCode() === Response::HTTP_OK) {
            $datas = \json_decode($res->getContent());

            return $datas;
        }

        return null;
    }

    // populate le bloc header de la requete
    private function computeHeaders(string &$url, array &$options = [], $isAdmin = false, $withoutToken = false): void
    {
        $session = $this->requestStack->getSession();

        // pas de token nécessaire et env de dév on ajoute juste le header http_basic
        if ($this->upplerEnv === 'dev') {
            $options['auth_basic'] = ['quantis', 'jj0tFWJulNYjDc'];
        }

        if ($withoutToken) {
            return;
        }

        // récupère le token admin ou user
        if ($isAdmin) {
            $this->getAdminToken();
            $accessToken = $this->adminToken;
        } else {
            if (
                !$session->has('account') || empty($session->get('account'))
                || !$session->has('access_token') || empty($session->get('access_token'))
            ) {
                throw new AuthenticationException('Session is not valid');
            }

            $accessToken = $session->get('access_token')->access_token;
        }

        if ($this->upplerEnv === 'dev') {
            // ajoute token admin à l'url
            $url .= (\preg_match('/([^?&=#]+)=([^&#]*)/', $url)) ? '&' : '?';
            $url .= 'access_token='.$accessToken;
        } else {
            // ajoute le Bearer token user dans le header
            $options['headers'] = [
                'Authorization' => 'Bearer '.$accessToken,
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
        $errorData = \json_decode($res->getContent(false));
        // le token a expiré
        if (
            !\is_object($errorData->error)
            && $errorData->error === 'invalid_grant'
        ) {
            // il s'agit d'une requête admin donc on renégocie un token admin et on relance la requete
            if ($isAdmin) {
                $this->getAdminToken(true);
            } else {
                // il s'agit d'un token user, on renégocie un token user,
                // on le remplace dans le hearder et on relance la requete
                $session = $this->requestStack->getSession();
                if (!$session->has('account')) {
                    throw new \Exception('account missing, token cannot renewed');
                }

                $account = $session->get('account');
                $accessToken = $this->getUserToken($account);
                // ici le remplacement du token user
                $options['headers'] = [
                    'Authorization' => 'Bearer '.$accessToken,
                ];
                $this->request($method, $url, $options);
            }
        } else {
            $this->apiLogger->critical('Error '.$res->getContent(false));
        }
    }
}
