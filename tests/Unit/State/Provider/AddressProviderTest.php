<?php

declare(strict_types=1);

use ApiPlatform\Metadata\GetCollection;
use App\Dto\Address;
use App\Factory\AddressFactory;
use App\Service\Djust\DjustAddressService;
use App\State\Provider\AddressProvider;

\uses()->group('UnitAddressProvider');

\beforeEach(function () {
    $this->addressFactory = Mockery::mock(AddressFactory::class);
    $this->djustAddressService = Mockery::mock(DjustAddressService::class);

    $this->provider = new AddressProvider(
        $this->addressFactory,
        $this->djustAddressService
    );
});

\afterEach(function () {
    Mockery::close();
});

\it('provides address collection from Djust service', function () {
    $djustAddresses = [
        [
            'externalId' => 'addr-1',
            'fullName' => 'John Doe',
            'address' => '123 Main St',
            'zipcode' => '75001',
            'city' => 'Paris',
            'country' => 'FR',
            'phone' => '0123456789',
            'billing' => true,
            'shipping' => false,
        ],
    ];

    $expectedAddresses = [
        (new Address())
            ->setId('addr-1')
            ->setFullName('John Doe')
            ->setAddress('123 Main St')
            ->setZipcode('75001')
            ->setCity('Paris')
            ->setCountry('FR')
            ->setPhone('0123456789')
            ->setBilling(true)
            ->setShipping(false),
    ];

    $this->djustAddressService->shouldReceive('getAddresses')
        ->once()
        ->andReturn($djustAddresses);

    $this->addressFactory->shouldReceive('createAndAddToCollection')
        ->once()
        ->with($djustAddresses)
        ->andReturn($expectedAddresses);

    $operation = new GetCollection();
    $result = $this->provider->provide($operation);

    \expect($result)->toBe($expectedAddresses)
        ->and($result)->toHaveCount(1)
        ->and($result[0]->getId())->toBe('addr-1');
});

\it('handles addresses with different billing/shipping types', function () {
    $djustAddresses = [
        [
            'externalId' => 'billing-only',
            'fullName' => 'Billing',
            'address' => '10 St',
            'zipcode' => '75010',
            'city' => 'Paris',
            'country' => 'FR',
            'phone' => '0100000001',
            'billing' => true,
            'shipping' => false,
        ],
        [
            'externalId' => 'both-types',
            'fullName' => 'Both',
            'address' => '20 Ave',
            'zipcode' => '75020',
            'city' => 'Paris',
            'country' => 'FR',
            'phone' => '0200000002',
            'billing' => true,
            'shipping' => true,
        ],
    ];

    $expectedAddresses = [
        (new Address())->setId('billing-only')->setBilling(true)->setShipping(false),
        (new Address())->setId('both-types')->setBilling(true)->setShipping(true),
    ];

    $this->djustAddressService->shouldReceive('getAddresses')
        ->once()
        ->andReturn($djustAddresses);

    $this->addressFactory->shouldReceive('createAndAddToCollection')
        ->once()
        ->andReturn($expectedAddresses);

    $operation = new GetCollection();
    $result = $this->provider->provide($operation);

    \expect($result)->toHaveCount(2)
        ->and($result[0]->isBilling())->toBeTrue()
        ->and($result[0]->isShipping())->toBeFalse()
        ->and($result[1]->isBilling())->toBeTrue()
        ->and($result[1]->isShipping())->toBeTrue();
});
