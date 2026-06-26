<?php

declare(strict_types=1);

use App\Dto\Address;
use App\Factory\AddressFactory;
use Psr\Cache\CacheItemPoolInterface;

\uses()->group('UnitAddressFactory');

\beforeEach(function () {
    $this->cache = Mockery::mock(CacheItemPoolInterface::class);
    $this->factory = new AddressFactory($this->cache);
});

\it('creates address with all fields', function () {
    $data = [
        'id' => '123',
        'externalId' => '123',
        'fullName' => 'John Doe',
        'address' => '123 Main St',
        'zipcode' => '75001',
        'city' => 'Paris',
        'country' => 'FR',
        'phone' => '+33123456789',
        'billing' => true,
        'shipping' => true,
    ];

    $result = $this->factory->create($data);

    \expect($result)->toBeInstanceOf(Address::class);
    \expect($result->getId())->toBe('123');
    \expect($result->getFullName())->toBe('John Doe');
    \expect($result->getAddress())->toBe('123 Main St');
    \expect($result->getZipcode())->toBe('75001');
    \expect($result->getCity())->toBe('Paris');
    \expect($result->getCountry())->toBe('FR');
    \expect($result->getPhone())->toBe('+33123456789');
    \expect($result->isShipping())->toBeTrue();
    \expect($result->isBilling())->toBeTrue();
});

\it('sets isBilling to true and isShipping to false', function () {
    $data = [
        'externalId' => '123',
        'fullName' => 'John Doe',
        'address' => '123 Main St',
        'zipcode' => '75001',
        'city' => 'Paris',
        'country' => 'FR',
        'phone' => '+33123456789',
        'billing' => true,
    ];

    $result = $this->factory->create($data);

    \expect($result->isBilling())->toBeTrue();
    \expect($result->isShipping())->toBeFalse();
});

\it('sets isShipping to true and isBilling to false', function () {
    $data = [
        'externalId' => '123',
        'fullName' => 'John Doe',
        'address' => '123 Main St',
        'zipcode' => '75001',
        'city' => 'Paris',
        'country' => 'FR',
        'phone' => '+33123456789',
        'shipping' => true,
    ];

    $result = $this->factory->create($data);

    \expect($result->isShipping())->toBeTrue();
    \expect($result->isBilling())->toBeFalse();
});

\it('uses id over externalId when both are present', function () {
    $data = [
        'id' => '456',
        'externalId' => '123',
        'fullName' => 'John Doe',
        'address' => '123 Main St',
        'zipcode' => '75001',
        'city' => 'Paris',
        'country' => 'FR',
        'phone' => '+33123456789',
        'billing' => true,
    ];

    $result = $this->factory->create($data);

    \expect($result->getId())->toBe('456');
});

\it('handles missing optional fields gracefully', function () {
    $data = [
        'id' => '789',
    ];

    $result = $this->factory->create($data);

    \expect($result->getId())->toBe('789');
    \expect($result->getFullName())->toBe('');
    \expect($result->getAddress())->toBe('');
    \expect($result->getCity())->toBe('');
    \expect($result->getCountry())->toBe('');
    \expect($result->getPhone())->toBeNull();
    \expect($result->isShipping())->toBeFalse();
    \expect($result->isBilling())->toBeFalse();
});
