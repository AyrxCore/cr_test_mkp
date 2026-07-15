<?php

declare(strict_types=1);

use App\Entity\Account;
use App\Service\Djust\DjustAuthenticationService;
use App\Service\Djust\DjustHttpClientService;
use App\Service\LogAccountConnectionService;
use Psr\Log\NullLogger;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\Uid\Uuid;

\beforeEach(function () {
    $this->httpClient = Mockery::mock(DjustHttpClientService::class);
    $this->requestStack = new RequestStack();
    $this->logger = new NullLogger();
    $this->logAccountConnectionService = Mockery::mock(LogAccountConnectionService::class);
    $this->session = new Session(new MockArraySessionStorage());

    $request = new Request();
    $request->setSession($this->session);
    $this->requestStack->push($request);

    $this->service = new DjustAuthenticationService(
        $this->httpClient,
        $this->requestStack,
        $this->logger,
        $this->logAccountConnectionService,
    );

    $this->account = Mockery::mock(Account::class);
    $this->accountId = Uuid::v6();
});

\afterEach(function () {
    Mockery::close();
});

\it('returns false when username is empty', function () {
    $this->account->shouldReceive('getId')->andReturn($this->accountId);
    $this->account->shouldReceive('getDjustUsername')->andReturn('');
    $this->account->shouldReceive('getDjustPassword')->andReturn('password123');

    $result = $this->service->authenticateUser($this->account);

    \expect($result)->toBeFalse();
})->group('DjustAuthenticationService', 'djust');

\it('returns false when password is empty', function () {
    $this->account->shouldReceive('getId')->andReturn($this->accountId);
    $this->account->shouldReceive('getDjustUsername')->andReturn('user@example.com');
    $this->account->shouldReceive('getDjustPassword')->andReturn('');

    $result = $this->service->authenticateUser($this->account);

    \expect($result)->toBeFalse();
})->group('DjustAuthenticationService', 'djust');

\it('returns false when both username and password are empty', function () {
    $this->account->shouldReceive('getId')->andReturn($this->accountId);
    $this->account->shouldReceive('getDjustUsername')->andReturn('');
    $this->account->shouldReceive('getDjustPassword')->andReturn('');

    $result = $this->service->authenticateUser($this->account);

    \expect($result)->toBeFalse();
})->group('DjustAuthenticationService', 'djust');

\it('successfully authenticates user with valid credentials', function () {
    $this->account->shouldReceive('getId')->andReturn($this->accountId);
    $this->account->shouldReceive('getDjustUsername')->andReturn('user@example.com');
    $this->account->shouldReceive('getDjustPassword')->andReturn('encrypted_password');
    $this->account->shouldReceive('getDjustCustomerAccountId')->andReturn('customer_123');

    $this->httpClient->shouldReceive('getValidAccountToken')
        ->once()
        ->andReturn('valid_token');

    $this->logAccountConnectionService->shouldReceive('createLog')
        ->once()
        ->with($this->account);

    $result = $this->service->authenticateUser($this->account);

    \expect($result)->toBeTrue();
})->group('DjustAuthenticationService', 'djust');

\it('returns false and cleans session when token retrieval fails', function () {
    $this->account->shouldReceive('getId')->andReturn($this->accountId);
    $this->account->shouldReceive('getDjustUsername')->andReturn('user@example.com');
    $this->account->shouldReceive('getDjustPassword')->andReturn('encrypted_password');
    $this->account->shouldReceive('getDjustCustomerAccountId')->andReturn('customer_123');

    $this->httpClient->shouldReceive('getValidAccountToken')
        ->once()
        ->andThrow(new BadRequestException('Invalid credentials'));

    $result = $this->service->authenticateUser($this->account);

    \expect($result)->toBeFalse();
})->group('DjustAuthenticationService', 'djust');

\it('successfully authenticates user and stores session data', function () {
    $this->account->shouldReceive('getId')->andReturn($this->accountId);
    $this->account->shouldReceive('getDjustUsername')->andReturn('user@example.com');
    $this->account->shouldReceive('getDjustPassword')->andReturn('encrypted_password');
    $this->account->shouldReceive('getDjustCustomerAccountId')->andReturn('customer_123');

    $this->httpClient->shouldReceive('getValidAccountToken')
        ->once()
        ->andReturn('valid_token');

    $this->logAccountConnectionService->shouldReceive('createLog')
        ->once()
        ->with($this->account);

    $result = $this->service->authenticateUser($this->account);

    \expect($result)->toBeTrue();
})->group('DjustAuthenticationService', 'djust');

\it('continues authentication when customer account is null', function () {
    $this->account->shouldReceive('getId')->andReturn($this->accountId);
    $this->account->shouldReceive('getDjustUsername')->andReturn('user@example.com');
    $this->account->shouldReceive('getDjustPassword')->andReturn('encrypted_password');
    $this->account->shouldReceive('getDjustCustomerAccountId')->andReturn('customer_123');

    $this->httpClient->shouldReceive('getValidAccountToken')
        ->once()
        ->andReturn('valid_token');

    $this->logAccountConnectionService->shouldReceive('createLog')
        ->once()
        ->with($this->account);

    $result = $this->service->authenticateUser($this->account);

    \expect($result)->toBeTrue();
})->group('DjustAuthenticationService', 'djust');

\it('handles generic exception during authentication', function () {
    $this->account->shouldReceive('getId')->andReturn($this->accountId);
    $this->account->shouldReceive('getDjustUsername')->andReturn('user@example.com');
    $this->account->shouldReceive('getDjustPassword')->andReturn('encrypted_password');
    $this->account->shouldReceive('getDjustCustomerAccountId')->andReturn('customer_123');

    $this->httpClient->shouldReceive('getValidAccountToken')
        ->once()
        ->andThrow(new \Exception('Unexpected error'));

    $result = $this->service->authenticateUser($this->account);

    \expect($result)->toBeFalse();
})->group('DjustAuthenticationService', 'djust');

\it('sets session data correctly before API calls', function () {
    $this->account->shouldReceive('getId')->andReturn($this->accountId);
    $this->account->shouldReceive('getDjustUsername')->andReturn('user@example.com');
    $this->account->shouldReceive('getDjustPassword')->andReturn('encrypted_password');
    $this->account->shouldReceive('getDjustCustomerAccountId')->andReturn('customer_123');

    $this->httpClient->shouldReceive('getValidAccountToken')
        ->once()
        ->andReturnUsing(function () {
            return 'valid_token';
        });

    $this->logAccountConnectionService->shouldReceive('createLog')
        ->once()
        ->with($this->account);

    $result = $this->service->authenticateUser($this->account);

    \expect($result)->toBeTrue();
})->group('DjustAuthenticationService', 'djust');

\it('does not log connection when isConnectionLogged is false', function () {
    $this->account->shouldReceive('getId')->andReturn($this->accountId);
    $this->account->shouldReceive('getDjustUsername')->andReturn('user@example.com');
    $this->account->shouldReceive('getDjustPassword')->andReturn('encrypted_password');
    $this->account->shouldReceive('getDjustCustomerAccountId')->andReturn('customer_123');

    $this->httpClient->shouldReceive('getValidAccountToken')
        ->once()
        ->andReturn('valid_token');

    $this->logAccountConnectionService->shouldNotReceive('createLog');

    $result = $this->service->authenticateUser($this->account, false);

    \expect($result)->toBeTrue();
})->group('DjustAuthenticationService', 'djust');
