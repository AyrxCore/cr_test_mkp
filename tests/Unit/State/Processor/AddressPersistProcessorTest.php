<?php

declare(strict_types=1);

namespace App\Tests\Unit\State\Processor;

use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Dto\Address;
use App\Factory\AddressFactory;
use App\Service\Djust\DjustAddressService;
use App\State\Processor\AddressPersistProcessor;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

\uses()->group('UnitAddressPersistProcessor');

\beforeEach(function () {
    $this->djustAddressService = \Mockery::mock(DjustAddressService::class);
    $this->addressFactory = \Mockery::mock(AddressFactory::class);
    $this->processor = new AddressPersistProcessor($this->djustAddressService, $this->addressFactory);
});

\afterEach(function () {
    \Mockery::close();
});

\it('updates address via Djust and returns data on Put operation', function () {
    $data = new Address();

    $this->djustAddressService
        ->shouldReceive('updateAddress')
        ->once()
        ->with($data)
        ->andReturn([]);

    $result = $this->processor->process($data, new Put());

    \expect($result)->toBe($data);
});

\it('creates address via Djust and returns Address DTO on Post operation', function () {
    $data = new Address();
    $djustResponse = ['id' => 'addr-123', 'externalId' => 'ext-456'];
    $created = new Address();

    $this->djustAddressService
        ->shouldReceive('createAddress')
        ->once()
        ->with($data)
        ->andReturn($djustResponse);

    $this->addressFactory
        ->shouldReceive('create')
        ->once()
        ->with($djustResponse)
        ->andReturn($created);

    $result = $this->processor->process($data, new Post());

    \expect($result)->toBe($created);
});

\it('throws BadRequestHttpException for unsupported operations', function () {
    $data = new Address();

    $this->djustAddressService->shouldNotReceive('updateAddress');
    $this->djustAddressService->shouldNotReceive('createAddress');

    $this->processor->process($data, new Delete());
})->throws(BadRequestHttpException::class);

\it('rethrows HttpException as-is without wrapping', function () {
    $data = new Address();
    $original = new NotFoundHttpException('Address not found.');

    $this->djustAddressService
        ->shouldReceive('updateAddress')
        ->once()
        ->andThrow($original);

    try {
        $this->processor->process($data, new Put());
    } catch (NotFoundHttpException $e) {
        \expect($e)->toBe($original);

        return;
    }

    \expect(false)->toBeTrue('Expected NotFoundHttpException was not thrown');
});

\it('wraps unexpected Throwable in BadRequestHttpException with generic message', function () {
    $data = new Address();

    $this->djustAddressService
        ->shouldReceive('updateAddress')
        ->once()
        ->andThrow(new \RuntimeException('Djust connection timeout'));

    $this->processor->process($data, new Put());
})->throws(BadRequestHttpException::class, 'Unable to persist address.');

\it('does not expose internal error message to client when wrapping Throwable', function () {
    $data = new Address();
    $internal = new \RuntimeException('Internal DB error with sensitive info');

    $this->djustAddressService
        ->shouldReceive('updateAddress')
        ->once()
        ->andThrow($internal);

    try {
        $this->processor->process($data, new Put());
    } catch (BadRequestHttpException $e) {
        \expect($e->getMessage())->toBe('Unable to persist address.');
        \expect($e->getPrevious())->toBe($internal);
    }
});
