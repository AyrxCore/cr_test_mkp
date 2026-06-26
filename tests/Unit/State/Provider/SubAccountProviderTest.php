<?php

declare(strict_types=1);

use ApiPlatform\Metadata\Get;
use App\Dto\SubAccount;
use App\Service\UpplerAccountService;
use App\State\Provider\SubAccountProvider;

\uses()->group('UnitSubAccountProvider', 'SubAccount');

\beforeEach(function () {
    $this->upplerAccountService = Mockery::mock(UpplerAccountService::class);
    $this->provider = new SubAccountProvider($this->upplerAccountService);
});

\afterEach(function () {
    Mockery::close();
});

\it('provides SubAccount data from UpplerAccountService', function () {
    $operation = new Get();
    $subAccountData = new stdClass();
    $subAccountData->id = 123;
    $subAccountData->email = 'john@example.com';
    $subAccountData->lastname = 'Doe';
    $subAccountData->firstname = 'John';
    $subAccountData->shipping_address = 1;
    $subAccountData->billing_address = 2;

    $this->upplerAccountService
        ->shouldReceive('getUserSubAccountData')
        ->once()
        ->andReturn($subAccountData);

    $result = $this->provider->provide($operation);

    \expect($result)->toBeInstanceOf(stdClass::class)
        ->and($result->id)->toBe(123)
        ->and($result->email)->toBe('john@example.com')
        ->and($result->firstname)->toBe('John')
        ->and($result->lastname)->toBe('Doe');
});

\it('passes operation, uriVariables and context to service', function () {
    $operation = new Get();
    $uriVariables = ['id' => '123'];
    $context = [];

    $subAccountData = new stdClass();
    $subAccountData->id = 123;

    $this->upplerAccountService
        ->shouldReceive('getUserSubAccountData')
        ->once()
        ->andReturn($subAccountData);

    $result = $this->provider->provide($operation, $uriVariables, $context);

    \expect($result)->toBeTruthy();
});

\it('returns null when UpplerAccountService returns null', function () {
    $operation = new Get();

    $this->upplerAccountService
        ->shouldReceive('getUserSubAccountData')
        ->once()
        ->andReturn(null);

    $result = $this->provider->provide($operation);

    \expect($result)->toBeNull();
});
