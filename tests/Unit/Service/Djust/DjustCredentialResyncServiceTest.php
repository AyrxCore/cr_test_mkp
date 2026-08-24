<?php

declare(strict_types=1);

use App\Service\Djust\DjustCredentialResyncService;
use App\Service\Djust\DjustOperatorApiService;
use Doctrine\DBAL\Connection;
use Psr\Log\NullLogger;

\beforeEach(function () {
    $this->connection = Mockery::mock(Connection::class);
    $this->operatorApi = Mockery::mock(DjustOperatorApiService::class);
    $this->logger = new NullLogger();

    $this->service = new DjustCredentialResyncService(
        $this->connection,
        $this->operatorApi,
        $this->logger,
    );
});

\afterEach(function () {
    Mockery::close();
});

\it('builds a local email index from accounts with djust_username', function () {
    $this->connection->shouldReceive('fetchAllAssociative')
        ->once()
        ->with('SELECT id, LOWER(djust_username) AS email FROM account WHERE djust_username IS NOT NULL')
        ->andReturn([
            ['id' => 'uuid-1', 'email' => 'user1@test.com'],
            ['id' => 'uuid-2', 'email' => 'user2@test.com'],
        ]);

    $result = $this->service->buildLocalEmailIndex();

    \expect($result)->toBe([
        'user1@test.com' => 'uuid-1',
        'user2@test.com' => 'uuid-2',
    ]);
})->group('DjustCredentialResyncService', 'djust');

\it('normalizes +test emails in the local index', function () {
    $this->connection->shouldReceive('fetchAllAssociative')
        ->once()
        ->andReturn([
            ['id' => 'uuid-1', 'email' => 'user+test@example.com'],
            ['id' => 'uuid-2', 'email' => 'other@example.com'],
        ]);

    $result = $this->service->buildLocalEmailIndex();

    \expect($result)->toBe([
        'user@example.com' => 'uuid-1',
        'other@example.com' => 'uuid-2',
    ]);
})->group('DjustCredentialResyncService', 'djust');

\it('returns empty index when no accounts have djust_username', function () {
    $this->connection->shouldReceive('fetchAllAssociative')
        ->once()
        ->andReturn([]);

    $result = $this->service->buildLocalEmailIndex();

    \expect($result)->toBeEmpty();
})->group('DjustCredentialResyncService', 'djust');

\it('strips +test tag from email', function () {
    \expect($this->service->stripTestTag('user+test@example.com'))->toBe('user@example.com');
    \expect($this->service->stripTestTag('other@example.com'))->toBe('other@example.com');
    \expect($this->service->stripTestTag('USER+TEST@EXAMPLE.COM'))->toBe('USER@EXAMPLE.COM');
})->group('DjustCredentialResyncService', 'djust');

\it('fetches preprod users by delegating to operator API', function () {
    $expected = [
        ['email' => 'user@test.com', 'id' => '0000000123', 'customerAccountId' => '0000000456'],
    ];

    $this->operatorApi->shouldReceive('fetchCustomerUsersBySearch')
        ->once()
        ->with('+test')
        ->andReturn($expected);

    $result = $this->service->fetchDjustPreprodUsers();

    \expect($result)->toBe($expected);
})->group('DjustCredentialResyncService', 'djust');

\it('batch updates accounts with all four djust fields', function () {
    $this->connection->shouldReceive('beginTransaction')->once();
    $this->connection->shouldReceive('executeStatement')
        ->twice()
        ->with(
            Mockery::on(static function (string $sql) {
                return \str_contains($sql, 'djust_customer_user_id')
                    && \str_contains($sql, 'djust_username')
                    && \str_contains($sql, 'djust_password')
                    && !\str_contains($sql, 'COALESCE');
            }),
            Mockery::on(static function (array $params) {
                return isset($params['djust_username'], $params['djust_password']);
            }),
        )
        ->andReturn(1);
    $this->connection->shouldReceive('commit')->once();

    $this->service->updateAccountDjustIdsBatch([
        ['accountId' => 'uuid-1', 'djustCustomerUserId' => '0000000123', 'djustCustomerAccountId' => '0000000456', 'djustUsername' => 'user+test@example.com', 'djustPassword' => 'encrypted-pwd'],
        ['accountId' => 'uuid-2', 'djustCustomerUserId' => '0000000789', 'djustCustomerAccountId' => null, 'djustUsername' => 'other+test@example.com', 'djustPassword' => 'encrypted-pwd'],
    ]);
})->group('DjustCredentialResyncService', 'djust');

\it('rolls back transaction on batch update failure', function () {
    $this->connection->shouldReceive('beginTransaction')->once();
    $this->connection->shouldReceive('executeStatement')->once()->andThrow(new RuntimeException('DB error'));
    $this->connection->shouldReceive('rollBack')->once();

    $service = $this->service;
    \expect(function () use ($service) {
        $service->updateAccountDjustIdsBatch([
            ['accountId' => 'uuid-1', 'djustCustomerUserId' => '0000000123', 'djustCustomerAccountId' => null, 'djustUsername' => 'user+test@example.com', 'djustPassword' => 'encrypted-pwd'],
        ]);
    })->toThrow(RuntimeException::class, 'DB error');
})->group('DjustCredentialResyncService', 'djust');

\it('does nothing when batch update receives empty array', function () {
    $this->connection->shouldReceive('beginTransaction')->never();

    $this->service->updateAccountDjustIdsBatch([]);
})->group('DjustCredentialResyncService', 'djust');
