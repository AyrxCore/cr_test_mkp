<?php

declare(strict_types=1);

use App\Dto\Address;
use App\Enum\Djust\DjustApiEndpoint;
use App\Service\Djust\DjustAddressService;
use App\Service\Djust\DjustHttpClientService;

\it('should get all addresses', function () {
    $mockHttpClient = Mockery::mock(DjustHttpClientService::class);

    $addresses = [
        [
            'id' => '1',
            'address' => '123 Main St',
            'city' => 'Paris',
            'zipCode' => '75001',
            'shipping' => true,
            'billing' => false,
        ],
        [
            'id' => '2',
            'address' => '456 Second Ave',
            'city' => 'Lyon',
            'zipCode' => '69001',
            'shipping' => false,
            'billing' => true,
        ],
        [
            'id' => '3',
            'address' => '789 Third Blvd',
            'city' => 'Marseille',
            'zipCode' => '13001',
            'shipping' => true,
            'billing' => true,
        ],
    ];

    $mockHttpClient
        ->shouldReceive('get')
        ->once()
        ->with(DjustApiEndpoint::SHOP_ADDRESSES->value)
        ->andReturn($addresses);

    $service = new DjustAddressService($mockHttpClient);

    $result = $service->getAddresses();

    \expect($result)->toHaveCount(3)
        ->and($result[0]['id'])->toBe('1')
        ->and($result[0]['shipping'])->toBeTrue()
        ->and($result[0]['billing'])->toBeFalse()
        ->and($result[1]['id'])->toBe('2')
        ->and($result[1]['shipping'])->toBeFalse()
        ->and($result[1]['billing'])->toBeTrue()
        ->and($result[2]['id'])->toBe('3')
        ->and($result[2]['shipping'])->toBeTrue()
        ->and($result[2]['billing'])->toBeTrue();
})->group('UnitDjustAddressService');

\it('should get a single address by id', function () {
    $mockHttpClient = Mockery::mock(DjustHttpClientService::class);

    $addressId = '123';
    $addresses = [
        [
            'id' => $addressId,
            'address' => '123 Main St',
            'city' => 'Paris',
            'zipCode' => '75001',
            'shipping' => true,
            'billing' => false,
        ],
        [
            'id' => '456',
            'address' => '456 Second Ave',
            'city' => 'Lyon',
            'zipCode' => '69001',
            'shipping' => false,
            'billing' => true,
        ],
    ];

    $mockHttpClient
        ->shouldReceive('get')
        ->once()
        ->with(DjustApiEndpoint::SHOP_ADDRESSES->value)
        ->andReturn($addresses);

    $service = new DjustAddressService($mockHttpClient);

    $result = $service->getAddress($addressId);

    \expect($result['id'])->toBe($addressId)
        ->and($result['shipping'])->toBeTrue()
        ->and($result['billing'])->toBeFalse();
})->group('UnitDjustAddressService');

\it('should throw exception when address not found by id', function () {
    $mockHttpClient = Mockery::mock(DjustHttpClientService::class);

    $mockHttpClient
        ->shouldReceive('get')
        ->once()
        ->with(DjustApiEndpoint::SHOP_ADDRESSES->value)
        ->andReturn([]);

    $service = new DjustAddressService($mockHttpClient);

    $service->getAddress('nonexistent');
})->throws(\Symfony\Component\HttpKernel\Exception\NotFoundHttpException::class, 'Address with id "nonexistent" not found')
->group('UnitDjustAddressService');

\it('should create a new address', function () {
    $mockHttpClient = Mockery::mock(DjustHttpClientService::class);

    $address = new Address();
    $address->setFullName('John Doe');
    $address->setAddress('123 Main St');
    $address->setZipcode('75001');
    $address->setCity('Paris');
    $address->setCountry('FR');
    $address->setPhone('0123456789');
    $address->setShipping(true);

    $expectedResponse = [
        'id' => '456',
        'fullName' => 'John Doe',
        'address' => '123 Main St',
    ];

    $mockHttpClient
        ->shouldReceive('post')
        ->once()
        ->with(DjustApiEndpoint::SHOP_ADDRESSES->value, [
            'fullName' => 'John Doe',
            'address' => '123 Main St',
            'zipcode' => '75001',
            'city' => 'Paris',
            'country' => 'FR',
            'phone' => '0123456789',
            'shipping' => true,
            'billing' => false,
        ])
        ->andReturn($expectedResponse);

    $service = new DjustAddressService($mockHttpClient);

    $result = $service->createAddress($address);

    \expect($result)->toBe($expectedResponse);
})->group('UnitDjustAddressService');

\it('should update an existing address', function () {
    $mockHttpClient = Mockery::mock(DjustHttpClientService::class);

    $address = new Address();
    $address->setId('123');
    $address->setFullName('Jane Doe');
    $address->setAddress('456 Second Ave');
    $address->setZipcode('69001');
    $address->setCity('Lyon');
    $address->setCountry('FR');
    $address->setPhone('0987654321');
    $address->setBilling(true);

    $expectedResponse = [
        'id' => '123',
        'fullName' => 'Jane Doe',
        'address' => '456 Second Ave',
    ];

    $mockHttpClient
        ->shouldReceive('put')
        ->once()
        ->with(\sprintf(DjustApiEndpoint::SHOP_ADDRESS_BY_ID->value, '123'), [
            'fullName' => 'Jane Doe',
            'address' => '456 Second Ave',
            'zipcode' => '69001',
            'city' => 'Lyon',
            'country' => 'FR',
            'phone' => '0987654321',
            'shipping' => false,
            'billing' => true,
        ])
        ->andReturn($expectedResponse);

    $service = new DjustAddressService($mockHttpClient);

    $result = $service->updateAddress($address);

    \expect($result)->toBe($expectedResponse);
})->group('UnitDjustAddressService');

\it('should delete an address', function () {
    $mockHttpClient = Mockery::mock(DjustHttpClientService::class);

    $addressId = '123';

    $mockHttpClient
        ->shouldReceive('delete')
        ->once()
        ->with(\sprintf(DjustApiEndpoint::SHOP_ADDRESS_BY_ID->value, $addressId))
        ->andReturn([]);

    $service = new DjustAddressService($mockHttpClient);

    $result = $service->deleteAddress($addressId);

    \expect($result)->toBe([]);
})->group('UnitDjustAddressService');
