<?php

declare(strict_types=1);

namespace App\Service\Djust;

use App\Entity\Account;
use App\Service\Account\CurrentAccountProvider;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class DjustAuthenticationService
{
    public function __construct(
        private readonly DjustHttpClientService $djustHttpClientService,
        private readonly RequestStack $requestStack,
        private readonly LoggerInterface $djustLogger,
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
            $this->djustHttpClientService->getValidAccountToken();

            if ($isConnectionLogged) {
                // TODO: Décommenter quand le service de log sera adapté pour Djust
                // $this->logAccountConnectionService->logAccount($account);
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

            return false;
        }
    }
}
