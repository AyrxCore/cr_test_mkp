<?php

declare(strict_types=1);

use ApiPlatform\Metadata\GetCollection;
use App\Dto\Djust\DjustSearchParams;
use App\Dto\Seller;
use App\Entity\Account;
use App\Factory\SellerFactory;
use App\Mapper\DjustSearchParamsMapper;
use App\Service\Account\CurrentAccountProvider;
use App\Service\Djust\DjustSellerService;
use App\State\Provider\SellerProvider;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

\uses()->group('UnitSellerProvider');

\beforeEach(function () {
    $this->djustSearchParamsMapper = Mockery::mock(DjustSearchParamsMapper::class);
    $this->djustSellerService = Mockery::mock(DjustSellerService::class);
    $this->sellerFactory = Mockery::mock(SellerFactory::class);
    $this->currentAccountProvider = Mockery::mock(CurrentAccountProvider::class);

    $this->provider = new SellerProvider(
        $this->djustSearchParamsMapper,
        $this->djustSellerService,
        $this->sellerFactory,
        $this->currentAccountProvider,
    );
});

\afterEach(function () {
    Mockery::close();
});

\it('orchestrates dependencies and returns seller DTOs when no account is provided', function () {
    $operation = new GetCollection();
    $context = ['filters' => ['query' => 'test']];

    $this->currentAccountProvider
        ->shouldReceive('getAccount')
        ->once()
        ->andReturn(null);

    $params = new DjustSearchParams(query: 'test');
    $this->djustSearchParamsMapper
        ->shouldReceive('fromContext')
        ->once()
        ->with($context)
        ->andReturn($params);

    $validSellers = [
        ['id' => '1', 'name' => 'RENAULT', 'supplierStatus' => 'ACTIVE'],
        ['id' => '2', 'name' => 'PEUGEOT', 'supplierStatus' => 'ACTIVE'],
    ];

    $this->djustSellerService
        ->shouldReceive('getValidSellers')
        ->once()
        ->with(null, Mockery::on(function ($arg) use ($params) {
            return $arg->query === $params->query;
        }))
        ->andReturn($validSellers);

    $seller1 = new Seller();
    $seller1->setId('1');
    $seller1->setName('RENAULT');

    $seller2 = new Seller();
    $seller2->setId('2');
    $seller2->setName('PEUGEOT');

    $sellerDtos = [$seller1, $seller2];

    $this->sellerFactory
        ->shouldReceive('createAndAddToCollection')
        ->once()
        ->with($validSellers, null)
        ->andReturn($sellerDtos);

    $result = $this->provider->provide($operation, [], $context);

    \expect($result)->toBeArray()
        ->and($result)->toHaveCount(2)
        ->and($result[0])->toBeInstanceOf(Seller::class)
        ->and($result[0]->getName())->toBe('RENAULT')
        ->and($result[1]->getName())->toBe('PEUGEOT');
});

\it('orchestrates dependencies with customer account id when account is provided', function () {
    $operation = new GetCollection();
    $context = ['filters' => []];

    $account = Mockery::mock(Account::class);
    $account->shouldReceive('getDjustCustomerAccountId')->andReturn('ACC-123');

    $this->currentAccountProvider
        ->shouldReceive('getAccount')
        ->once()
        ->andReturn($account);

    $params = new DjustSearchParams();
    $this->djustSearchParamsMapper
        ->shouldReceive('fromContext')
        ->once()
        ->with($context)
        ->andReturn($params);

    $validSellers = [
        ['id' => '1', 'name' => 'SELLER-1', 'supplierStatus' => 'ACTIVE'],
    ];

    $this->djustSellerService
        ->shouldReceive('getValidSellers')
        ->once()
        ->with('ACC-123', Mockery::type(DjustSearchParams::class))
        ->andReturn($validSellers);

    $seller = new Seller();
    $seller->setId('1');
    $seller->setName('SELLER-1');

    $this->sellerFactory
        ->shouldReceive('createAndAddToCollection')
        ->once()
        ->with($validSellers, 'ACC-123')
        ->andReturn([$seller]);

    $result = $this->provider->provide($operation, [], $context);

    \expect($result)->toBeArray()
        ->and($result)->toHaveCount(1)
        ->and($result[0]->getId())->toBe('1');
});

\it('returns empty array when no valid sellers are found', function () {
    $operation = new GetCollection();
    $context = ['filters' => []];

    $this->currentAccountProvider
        ->shouldReceive('getAccount')
        ->once()
        ->andReturn(null);

    $this->djustSearchParamsMapper
        ->shouldReceive('fromContext')
        ->once()
        ->with($context)
        ->andReturn(new DjustSearchParams());

    $this->djustSellerService
        ->shouldReceive('getValidSellers')
        ->once()
        ->with(null, Mockery::type(DjustSearchParams::class))
        ->andReturn([]);

    $this->sellerFactory
        ->shouldReceive('createAndAddToCollection')
        ->once()
        ->with([], null)
        ->andReturn([]);

    $result = $this->provider->provide($operation, [], $context);

    \expect($result)->toBeArray()->toBeEmpty();
});

\it('throws BadRequestHttpException when an error occurs', function () {
    $operation = new GetCollection();
    $context = ['filters' => []];

    $this->currentAccountProvider
        ->shouldReceive('getAccount')
        ->once()
        ->andReturn(null);

    $this->djustSearchParamsMapper
        ->shouldReceive('fromContext')
        ->once()
        ->with($context)
        ->andReturn(new DjustSearchParams());

    $this->djustSellerService
        ->shouldReceive('getValidSellers')
        ->once()
        ->andThrow(new \RuntimeException('Djust API error'));

    $this->provider->provide($operation, [], $context);
})->throws(BadRequestHttpException::class, 'An error occurred while retrieving the sellers: Djust API error');

\it('returns only active sellers filtered by getValidSellers', function () {
    $operation = new GetCollection();
    $context = ['filters' => []];

    $this->currentAccountProvider
        ->shouldReceive('getAccount')
        ->once()
        ->andReturn(null);

    $this->djustSearchParamsMapper
        ->shouldReceive('fromContext')
        ->once()
        ->with($context)
        ->andReturn(new DjustSearchParams());

    $validSellers = [
        ['id' => '1', 'name' => 'ACTIVE-SELLER', 'supplierStatus' => 'ACTIVE'],
        ['id' => '2', 'name' => 'ANOTHER-ACTIVE', 'supplierStatus' => 'ACTIVE'],
    ];

    $this->djustSellerService
        ->shouldReceive('getValidSellers')
        ->once()
        ->with(null, Mockery::type(DjustSearchParams::class))
        ->andReturn($validSellers);

    $seller1 = new Seller();
    $seller1->setId('1');
    $seller1->setName('ACTIVE-SELLER');

    $seller2 = new Seller();
    $seller2->setId('2');
    $seller2->setName('ANOTHER-ACTIVE');

    $this->sellerFactory
        ->shouldReceive('createAndAddToCollection')
        ->once()
        ->with($validSellers, null)
        ->andReturn([$seller1, $seller2]);

    $result = $this->provider->provide($operation, [], $context);

    \expect($result)->toBeArray()
        ->and($result)->toHaveCount(2)
        ->and($result[0]->getId())->toBe('1')
        ->and($result[0]->getName())->toBe('ACTIVE-SELLER')
        ->and($result[1]->getId())->toBe('2')
        ->and($result[1]->getName())->toBe('ANOTHER-ACTIVE');
});

