<?php

declare(strict_types=1);

namespace App\Service\Djust;

use App\Enum\Djust\DjustApiEndpoint;
use App\Service\Account\CurrentAccountProvider;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class DjustCustomerAccountService
{
    public const string SESSION_KEY_CUSTOMER_ACCOUNT = 'djust_customer_account';
    public const string SESSION_KEY_CACHED_AT = 'djust_customer_account_cached_at';
    private const int CACHE_TTL_SECONDS = 60;

    public function __construct(
        private readonly DjustHttpClientService $djustHttpClientService,
        private readonly RequestStack $requestStack,
        private readonly LoggerInterface $djustLogger,
    ) {
    }

    public function clearCache(): void
    {
        $session = $this->requestStack->getSession();
        $session->remove(self::SESSION_KEY_CUSTOMER_ACCOUNT);
        $session->remove(self::SESSION_KEY_CACHED_AT);
    }

    public function getCustomerAccount(bool $forceRefresh = false): ?array
    {
        try {
            $session = $this->requestStack->getSession();

            $accountData = $session->get(CurrentAccountProvider::SESSION_KEY_ACCOUNT);
            if (!$accountData) {
                return null;
            }

            if (!$forceRefresh) {
                $cachedCustomerAccount = $session->get(self::SESSION_KEY_CUSTOMER_ACCOUNT);
                $cachedAt = $session->get(self::SESSION_KEY_CACHED_AT);

                if ($cachedCustomerAccount !== null && $cachedAt !== null) {
                    if ((\time() - $cachedAt) < self::CACHE_TTL_SECONDS) {
                        return $cachedCustomerAccount;
                    }
                }
            }

            $customerAccount = $this->djustHttpClientService->get(
                DjustApiEndpoint::SHOP_CUSTOMER_ACCOUNTS->value
            );

            $session->set(self::SESSION_KEY_CUSTOMER_ACCOUNT, $customerAccount);
            $session->set(self::SESSION_KEY_CACHED_AT, \time());

            return $customerAccount;
        } catch (\Throwable $e) {
            $this->djustLogger->warning('Erreur lors de la récupération du customer account', [
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    public function getUserTags(): array
    {
        try {
            $customerAccount = $this->getCustomerAccount();

            if ($customerAccount === null) {
                return [];
            }

            return $this->extractTagsFromCustomerAccount($customerAccount);
        } catch (\Throwable $e) {
            $this->djustLogger->warning('Erreur lors de la récupération des tags utilisateur', [
                'error' => $e->getMessage(),
            ]);

            return [];
        }
    }

    private function extractTagsFromCustomerAccount(array $customerAccount): array
    {
        if (!isset($customerAccount['customerTags']) || !\is_array($customerAccount['customerTags'])) {
            return [];
        }

        $tags = [];
        foreach ($customerAccount['customerTags'] as $tagData) {
            if (isset($tagData['id']) && $tagData['id'] !== '') {
                $trimmedId = \trim($tagData['id']);
                if ($trimmedId !== '') {
                    $tags[] = $trimmedId;
                }
            }
        }

        return \array_unique($tags);
    }
}
