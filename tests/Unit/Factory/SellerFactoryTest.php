<?php

declare(strict_types=1);

use App\Dto\Seller;
use App\Factory\SellerFactory;
use Psr\Cache\CacheItemPoolInterface;

\uses()->group('UnitSellerFactory');

\beforeEach(function () {
    $this->cache = Mockery::mock(CacheItemPoolInterface::class);
    $this->factory = new SellerFactory($this->cache);
});

\afterEach(function () {
    Mockery::close();
});

\it('creates a Seller DTO from raw data', function () {
    $data = [
        'id' => 'SELLER-1',
        'externalId' => 'EXT-1',
        'name' => 'Seller One',
        'returnPolicy' => 'Return in 30 days',
        'logo' => 'https://example.com/logo.png',
        'customFieldValues' => [
            ['customField' => ['externalId' => 'supplier_cgv'], 'value' => ['value' => 'CGV content']],
            ['customField' => ['externalId' => 'supplier_delivery_info'], 'value' => ['value' => 'Delivery info']],
        ],
    ];

    $result = $this->factory->create($data);

    \expect($result)->toBeInstanceOf(Seller::class)
        ->and($result->getId())->toBe('SELLER-1')
        ->and($result->getExternalId())->toBe('EXT-1')
        ->and($result->getName())->toBe('Seller One')
        ->and($result->getDescription())->toBe('Return in 30 days')
        ->and($result->getAvatar())->toBe('https://example.com/logo.png')
        ->and($result->getTos())->toBe('CGV content')
        ->and($result->getSupplierDeliveryInfo())->toBe('Delivery info');
});

\it('creates a Seller with null fields when data is missing', function () {
    $data = [
        'id' => 'SELLER-1',
    ];

    $result = $this->factory->create($data);

    \expect($result)->toBeInstanceOf(Seller::class)
        ->and($result->getId())->toBe('SELLER-1')
        ->and($result->getExternalId())->toBeNull()
        ->and($result->getName())->toBe('')
        ->and($result->getDescription())->toBe('')
        ->and($result->getAvatar())->toBeNull()
        ->and($result->getTos())->toBeNull()
        ->and($result->getSupplierDeliveryInfo())->toBeNull();
});

\it('handles null customFieldValues without crashing', function () {
    $data = [
        'id' => 'SELLER-1',
        'name' => 'Seller',
        'customFieldValues' => null,
    ];

    $result = $this->factory->create($data);

    \expect($result)->toBeInstanceOf(Seller::class)
        ->and($result->getTos())->toBeNull()
        ->and($result->getSupplierDeliveryInfo())->toBeNull();
});

\it('creates a collection of Seller DTOs', function () {
    $data = [
        ['id' => 'SELLER-1', 'name' => 'Seller One'],
        ['id' => 'SELLER-2', 'name' => 'Seller Two'],
    ];

    $result = $this->factory->createAndAddToCollection($data);

    \expect($result)->toHaveCount(2)
        ->and($result[0])->toBeInstanceOf(Seller::class)
        ->and($result[0]->getId())->toBe('SELLER-1')
        ->and($result[1]->getId())->toBe('SELLER-2');
});

\it('returns empty array for empty collection', function () {
    $result = $this->factory->createAndAddToCollection([]);

    \expect($result)->toBeArray()->toBeEmpty();
});
