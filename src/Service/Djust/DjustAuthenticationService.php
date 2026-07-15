<?php

declare(strict_types=1);

namespace App\Service\Djust;

use App\Entity\Account;
use App\Service\Account\CurrentAccountProvider;
use App\Service\LogAccountConnectionService;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class DjustAuthenticationService
{
    public function __construct(
        private readonly DjustHttpClientService $djustHttpClientService,
        private readonly RequestStack $requestStack,
        private readonly LoggerInterface $djustLogger,
        private readonly LogAccountConnectionService $logAccountConnectionService
    ) {
    }

    public function authenticateUser(Account $account, bool $isConnectionLogged = true): bool
    {
        if (empty($account->getDjustUsername()) || empty($account->getDjustPassword())) {
            $this->djustLogger->warning('Tentative d\'authentification avec identifiants manquants', [
                'account_id' => $account->getId(),
            ]);

            return false;
        }

        $session = $this->requestStack->getSession();

        try {
            $accessToken = $this->djustHttpClientService->getValidAccountToken();

            $tokenObject = (object) ['access_token' => $accessToken];
            $session->set('access_token', $tokenObject);

            if ($isConnectionLogged) {
                 $this->logAccountConnectionService->createLog($account);
            }

            $this->djustLogger->info('Authentification Djust réussie', [
                'account_id' => $account->getId(),
                'username' => $account->getDjustUsername(),
            ]);

            return true;
        } catch (\Throwable $e) {
            $this->djustLogger->error('Échec de l\'authentification Djust', [
                'account_id' => $account->getId(),
                'username' => $account->getDjustUsername(),
                'error' => $e->getMessage(),
            ]);

            $session->remove(CurrentAccountProvider::SESSION_KEY_ACCOUNT);
            $session->remove('access_token');

            return false;
        }
    }
}
