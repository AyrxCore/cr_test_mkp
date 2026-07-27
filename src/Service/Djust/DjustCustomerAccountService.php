<?php

declare(strict_types=1);

namespace App\Service\Djust;

use App\Enum\Djust\DjustApiEndpoint;
use App\Service\Account\CurrentAccountProvider;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\HttpClient\Exception\ExceptionInterface as HttpClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\HttpExceptionInterface;

class DjustCustomerAccountService
{
    public const string SESSION_KEY_CUSTOMER_ACCOUNT = 'djust_customer_account';
    public const string SESSION_KEY_CACHED_AT = 'djust_customer_account_cached_at';
    private const int CACHE_TTL_SECONDS = 60;
    private const int MAX_FETCH_ATTEMPTS = 3;
    private const int RETRY_DELAY_MICROSECONDS = 400_000;

    public function __construct(
        private readonly DjustHttpClientService $djustHttpClientService,
        private readonly RequestStack $requestStack,
        private readonly LoggerInterface $djustLogger,
        private readonly int $retryDelayMicroseconds = self::RETRY_DELAY_MICROSECONDS,
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

            $customerAccount = $this->fetchCustomerAccountWithRetry();
            if ($customerAccount === null) {
                return null;
            }

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

    /**
     * Djust peut renvoyer une erreur juste après une authentification fraîche (propagation
     * du token côté Djust). Un court retry reproduit ce qu'un simple refresh corrige déjà.
     */
    private function fetchCustomerAccountWithRetry(): ?array
    {
        $lastError = null;

        for ($attempt = 1; $attempt <= self::MAX_FETCH_ATTEMPTS; ++$attempt) {
            try {
                return $this->djustHttpClientService->get(
                    DjustApiEndpoint::SHOP_CUSTOMER_ACCOUNTS->value
                );
            } catch (HttpClientExceptionInterface $e) {
                $lastError = $e;

                if ($attempt < self::MAX_FETCH_ATTEMPTS) {
                    \usleep($this->retryDelayMicroseconds);
                }
            }
        }

        $responseBody = null;
        if ($lastError instanceof HttpExceptionInterface) {
            try {
                $responseBody = $lastError->getResponse()->getContent(false);
            } catch (\Throwable) {
                $responseBody = null;
            }
        }

        $this->djustLogger->warning('Erreur lors de la récupération du customer account après plusieurs tentatives', [
            'error' => $lastError?->getMessage(),
            'error_type' => $lastError ? $lastError::class : null,
            'response_body' => $responseBody,
            'attempts' => self::MAX_FETCH_ATTEMPTS,
        ]);

        return null;
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
