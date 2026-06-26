<?php

declare(strict_types=1);

use App\Service\Account\CurrentAccountProvider;
use App\Service\Djust\DjustCustomerAccountService;
use App\Service\Djust\DjustHttpClientService;
use Psr\Log\NullLogger;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

\beforeEach(function () {
    $this->httpClient = Mockery::mock(DjustHttpClientService::class);
    $this->requestStack = new RequestStack();
    $this->logger = new NullLogger();
    $this->session = new Session(new MockArraySessionStorage());

    $request = new Request();
    $request->setSession($this->session);
    $this->requestStack->push($request);

    $this->service = new DjustCustomerAccountService(
        $this->httpClient,
        $this->requestStack,
        $this->logger
    );
});

\afterEach(function () {
    Mockery::close();
});

\it('returns customer account from session when cached', function () {
    // Configurer les données de session Djust (nécessaire pour la vérification)
    $this->session->set(CurrentAccountProvider::SESSION_KEY_ACCOUNT, [
        'id' => 'account-uuid',
        'username' => 'user@example.com',
        'password' => 'encrypted_password',
        'customerAccountId' => 'customer_123',
    ]);

    $cachedAccount = [
        'id' => '123',
        'name' => 'Test Account',
        'customerTags' => [
            ['name' => 'VIP'],
            ['name' => 'Premium'],
        ],
    ];

    $this->session->set(DjustCustomerAccountService::SESSION_KEY_CUSTOMER_ACCOUNT, $cachedAccount);
    $this->session->set(DjustCustomerAccountService::SESSION_KEY_CACHED_AT, \time());

    $result = $this->service->getCustomerAccount();

    \expect($result)->toBe($cachedAccount);
})->group('DjustCustomerAccountService', 'djust');

\it('fetches customer account from API when not cached', function () {
    // Configurer les données de session Djust (nécessaire pour l'appel API)
    $this->session->set(CurrentAccountProvider::SESSION_KEY_ACCOUNT, [
        'id' => 'account-uuid',
        'username' => 'user@example.com',
        'password' => 'encrypted_password',
        'customerAccountId' => 'customer_123',
    ]);

    $apiAccount = [
        'id' => '456',
        'name' => 'API Account',
        'customerTags' => [
            ['name' => 'Standard'],
        ],
    ];

    $this->httpClient->shouldReceive('get')
        ->once()
        ->with('/v1/shop/customer-accounts')
        ->andReturn($apiAccount);

    $result = $this->service->getCustomerAccount();

    \expect($result)->toBe($apiAccount)
        ->and($this->session->get(DjustCustomerAccountService::SESSION_KEY_CUSTOMER_ACCOUNT))->toBe($apiAccount);
})->group('DjustCustomerAccountService', 'djust');

\it('forces refresh when forceRefresh is true', function () {
    // Configurer les données de session Djust (nécessaire pour l'appel API)
    $this->session->set(CurrentAccountProvider::SESSION_KEY_ACCOUNT, [
        'id' => 'account-uuid',
        'username' => 'user@example.com',
        'password' => 'encrypted_password',
        'customerAccountId' => 'customer_123',
    ]);

    $cachedAccount = ['id' => '123', 'name' => 'Cached'];
    $freshAccount = ['id' => '456', 'name' => 'Fresh'];

    $this->session->set(DjustCustomerAccountService::SESSION_KEY_CUSTOMER_ACCOUNT, $cachedAccount);

    $this->httpClient->shouldReceive('get')
        ->once()
        ->with('/v1/shop/customer-accounts')
        ->andReturn($freshAccount);

    $result = $this->service->getCustomerAccount(true);

    \expect($result)->toBe($freshAccount);
})->group('DjustCustomerAccountService', 'djust');

\it('returns null when no account data in session', function () {
    // Pas de SESSION_KEY_ACCOUNT configuré
    $result = $this->service->getCustomerAccount();

    \expect($result)->toBeNull();
})->group('DjustCustomerAccountService', 'djust');

\it('returns null and logs warning when API call fails', function () {
    // Configurer les données de session Djust (nécessaire pour l'appel API)
    $this->session->set(CurrentAccountProvider::SESSION_KEY_ACCOUNT, [
        'id' => 'account-uuid',
        'username' => 'user@example.com',
        'password' => 'encrypted_password',
        'customerAccountId' => 'customer_123',
    ]);

    $this->httpClient->shouldReceive('get')
        ->once()
        ->andThrow(new \RuntimeException('API Error'));

    $result = $this->service->getCustomerAccount();

    \expect($result)->toBeNull();
})->group('DjustCustomerAccountService', 'djust');

\it('extracts tags IDs from customer account', function () {
    // Configurer les données de session Djust (nécessaire pour la vérification)
    $this->session->set(CurrentAccountProvider::SESSION_KEY_ACCOUNT, [
        'id' => 'account-uuid',
        'username' => 'user@example.com',
        'password' => 'encrypted_password',
        'customerAccountId' => 'customer_123',
    ]);

    $customerAccount = [
        'id' => '123',
        'customerTags' => [
            ['id' => 'vip-uuid-1'],
            ['id' => 'premium-uuid-2'],
            ['id' => 'gold-uuid-3'],
            ['id' => 'vip-uuid-1'], // Duplicate
        ],
    ];

    $this->session->set(DjustCustomerAccountService::SESSION_KEY_CUSTOMER_ACCOUNT, $customerAccount);
    $this->session->set(DjustCustomerAccountService::SESSION_KEY_CACHED_AT, \time());

    $result = $this->service->getUserTags();

    \expect($result)->toBeArray()
        ->toHaveCount(3)
        ->toContain('vip-uuid-1')
        ->toContain('premium-uuid-2')
        ->toContain('gold-uuid-3');
})->group('DjustCustomerAccountService', 'djust');

\it('returns empty array when customer account has no tags', function () {
    // Configurer les données de session Djust (nécessaire pour la vérification)
    $this->session->set(CurrentAccountProvider::SESSION_KEY_ACCOUNT, [
        'id' => 'account-uuid',
        'username' => 'user@example.com',
        'password' => 'encrypted_password',
        'customerAccountId' => 'customer_123',
    ]);

    $customerAccount = [
        'id' => '123',
        'customerTags' => [],
    ];

    $this->session->set(DjustCustomerAccountService::SESSION_KEY_CUSTOMER_ACCOUNT, $customerAccount);
    $this->session->set(DjustCustomerAccountService::SESSION_KEY_CACHED_AT, \time());

    $result = $this->service->getUserTags();

    \expect($result)->toBeArray()->toBeEmpty();
})->group('DjustCustomerAccountService', 'djust');

\it('returns empty array when no account data in session', function () {
    // Pas de SESSION_KEY_ACCOUNT configuré
    $result = $this->service->getUserTags();

    \expect($result)->toBeArray()->toBeEmpty();
})->group('DjustCustomerAccountService', 'djust');

\it('caches tags in memory for subsequent calls', function () {
    // Configurer les données de session Djust (nécessaire pour la vérification)
    $this->session->set(CurrentAccountProvider::SESSION_KEY_ACCOUNT, [
        'id' => 'account-uuid',
        'username' => 'user@example.com',
        'password' => 'encrypted_password',
        'customerAccountId' => 'customer_123',
    ]);

    $customerAccount = [
        'id' => '123',
        'customerTags' => [
            ['id' => 'vip-uuid'],
        ],
    ];

    $this->session->set(DjustCustomerAccountService::SESSION_KEY_CUSTOMER_ACCOUNT, $customerAccount);
    $this->session->set(DjustCustomerAccountService::SESSION_KEY_CACHED_AT, \time());

    // Premier appel
    $result1 = $this->service->getUserTags();

    // Deuxième appel (devrait utiliser le cache mémoire, pas refaire l'extraction)
    $result2 = $this->service->getUserTags();

    \expect($result1)->toBe($result2)
        ->and($result1)->toContain('vip-uuid');
})->group('DjustCustomerAccountService', 'djust');

\it('invalidates memory cache when customer account is refreshed', function () {
    // Configurer les données de session Djust (nécessaire pour l'appel API)
    $this->session->set(CurrentAccountProvider::SESSION_KEY_ACCOUNT, [
        'id' => 'account-uuid',
        'username' => 'user@example.com',
        'password' => 'encrypted_password',
        'customerAccountId' => 'customer_123',
    ]);

    $oldAccount = [
        'id' => '123',
        'customerTags' => [['id' => 'old-tag-uuid']],
    ];

    $newAccount = [
        'id' => '123',
        'customerTags' => [['id' => 'new-tag-uuid']],
    ];

    $this->session->set(DjustCustomerAccountService::SESSION_KEY_CUSTOMER_ACCOUNT, $oldAccount);
    $this->session->set(DjustCustomerAccountService::SESSION_KEY_CACHED_AT, \time());

    // Premier appel - cache les tags "old-tag-uuid"
    $tags1 = $this->service->getUserTags();
    \expect($tags1)->toContain('old-tag-uuid');

    // Rafraîchir le compte
    $this->httpClient->shouldReceive('get')
        ->once()
        ->andReturn($newAccount);

    $this->service->getCustomerAccount(true);

    // Deuxième appel - devrait retourner les nouveaux tags "new-tag-uuid"
    $tags2 = $this->service->getUserTags();
    \expect($tags2)->toContain('new-tag-uuid')
        ->and($tags2)->not->toContain('old-tag-uuid');
})->group('DjustCustomerAccountService', 'djust');

\it('handles invalid customerTags structure gracefully', function () {
    // Configurer les données de session Djust (nécessaire pour la vérification)
    $this->session->set(CurrentAccountProvider::SESSION_KEY_ACCOUNT, [
        'id' => 'account-uuid',
        'username' => 'user@example.com',
        'password' => 'encrypted_password',
        'customerAccountId' => 'customer_123',
    ]);

    $customerAccount = [
        'id' => '123',
        'customerTags' => 'invalid_not_array',
    ];

    $this->session->set(DjustCustomerAccountService::SESSION_KEY_CUSTOMER_ACCOUNT, $customerAccount);
    $this->session->set(DjustCustomerAccountService::SESSION_KEY_CACHED_AT, \time());

    $result = $this->service->getUserTags();

    \expect($result)->toBeArray()->toBeEmpty();
})->group('DjustCustomerAccountService', 'djust');

\it('filters out tags with empty ids', function () {
    // Configurer les données de session Djust (nécessaire pour la vérification)
    $this->session->set(CurrentAccountProvider::SESSION_KEY_ACCOUNT, [
        'id' => 'account-uuid',
        'username' => 'user@example.com',
        'password' => 'encrypted_password',
        'customerAccountId' => 'customer_123',
    ]);

    $customerAccount = [
        'id' => '123',
        'customerTags' => [
            ['id' => 'valid-tag-uuid'],
            ['id' => ''],
            ['id' => '   '], // Trimmed to empty, should be filtered
            ['other_field' => 'no_id'], // No 'id' key, should be filtered
        ],
    ];

    $this->session->set(DjustCustomerAccountService::SESSION_KEY_CUSTOMER_ACCOUNT, $customerAccount);
    $this->session->set(DjustCustomerAccountService::SESSION_KEY_CACHED_AT, \time());

    $result = $this->service->getUserTags();

    // Les tags vides sont filtrés
    \expect($result)->toBeArray()
        ->toHaveCount(1)
        ->toContain('valid-tag-uuid');
})->group('DjustCustomerAccountService', 'djust');
