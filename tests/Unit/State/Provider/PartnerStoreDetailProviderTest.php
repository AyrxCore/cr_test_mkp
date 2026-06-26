<?php

declare(strict_types=1);

use ApiPlatform\Metadata\Get;
use App\Dto\StoreDetailDto;
use App\Entity\Accord;
use App\Entity\Partner;
use App\Entity\PartnerStore;
use App\Helper\Formatter\PhoneFormatter;
use App\Repository\PartnerStoreRepository;
use App\Service\Account\CurrentAccountProvider;
use App\Service\Djust\DjustSellerService;
use App\State\Provider\PartnerStoreDetailProvider;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityNotFoundException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Uid\Uuid;

uses()->group('UnitPartnerStoreDetailProvider');

beforeEach(function () {
    $this->repository = Mockery::mock(PartnerStoreRepository::class);
    $this->phoneFormatter = Mockery::mock(PhoneFormatter::class);
    $this->logger = Mockery::mock(LoggerInterface::class);
    $this->djustSellerService = Mockery::mock(DjustSellerService::class);
    $this->currentAccountProvider = Mockery::mock(CurrentAccountProvider::class);

    $this->provider = new PartnerStoreDetailProvider(
        $this->repository,
        $this->phoneFormatter,
        $this->logger,
        $this->djustSellerService,
        $this->currentAccountProvider,
    );
});

afterEach(function () {
    Mockery::close();
});

it('throws if store not found', function () {
    $this->repository->shouldReceive('find')->once()->andReturn(null);

    $this->provider->provide(new Get(), ['id' => 'some-id']);
})->throws(EntityNotFoundException::class);

it('throws if no store id provided', function () {
    $this->provider->provide(new Get(), []);
})->throws(EntityNotFoundException::class);

it('fetches logo from seller service when no accord logo', function () {
    $partnerId = Uuid::fromString('4c4f6064-5827-11ec-999d-028bf10cd626');

    $partner = Mockery::mock(Partner::class);
    $partner->shouldReceive('getId')->andReturn($partnerId);
    $partner->shouldReceive('getName')->andReturn('ACTUAL LEADER GROUP');
    $partner->shouldReceive('getAccords')->andReturn(new ArrayCollection());

    $store = Mockery::mock(PartnerStore::class);
    $store->shouldReceive('getId')->andReturn(Uuid::v4());
    $store->shouldReceive('getName')->andReturn('Magasin Paris');
    $store->shouldReceive('getAddress')->andReturn('1 rue de la Paix');
    $store->shouldReceive('getPhone')->andReturn('0123456789');
    $store->shouldReceive('getLatitude')->andReturn('48.8566');
    $store->shouldReceive('getLongitude')->andReturn('2.3522');
    $store->shouldReceive('getPartner')->andReturn($partner);

    $this->repository->shouldReceive('find')->once()->andReturn($store);
    $this->phoneFormatter->shouldReceive('format')->andReturn('01 23 45 67 89');
    $this->currentAccountProvider->shouldReceive('getAccount')->once()->andReturn(null);
    $this->djustSellerService
        ->shouldReceive('getSellerLogo')
        ->once()
        ->with('4c4f6064-5827-11ec-999d-028bf10cd626', null)
        ->andReturn('https://cdn.djust.fr/logo.png');

    $result = $this->provider->provide(new Get(), ['id' => 'store-id']);

    expect($result)->toBeInstanceOf(StoreDetailDto::class)
        ->and($result->djustId)->toBe('4c4f6064-5827-11ec-999d-028bf10cd626')
        ->and($result->logo)->toBe('https://cdn.djust.fr/logo.png');
});

it('uses accord logo over djust logo', function () {
    $partnerId = Uuid::v4();

    $accord = Mockery::mock(Accord::class);
    $accord->shouldReceive('getLogo')->andReturn('https://cdn.example.com/accord-logo.png');
    $accord->shouldReceive('getName')->andReturn('Accord Test');
    $accord->shouldReceive('getId')->andReturn(Uuid::v4());

    $store = Mockery::mock(PartnerStore::class);
    $storeId = Uuid::v4();
    $store->shouldReceive('getId')->andReturn($storeId);
    $store->shouldReceive('getName')->andReturn('Magasin Lyon');
    $store->shouldReceive('getAddress')->andReturn('2 rue de la République');
    $store->shouldReceive('getPhone')->andReturn('0456789012');
    $store->shouldReceive('getLatitude')->andReturn('45.7640');
    $store->shouldReceive('getLongitude')->andReturn('4.8357');

    $accord->shouldReceive('getStores')->andReturn(new ArrayCollection([$store]));

    $partner = Mockery::mock(Partner::class);
    $partner->shouldReceive('getId')->andReturn($partnerId);
    $partner->shouldReceive('getName')->andReturn('Partenaire Test');
    $partner->shouldReceive('getAccords')->andReturn(new ArrayCollection([$accord]));

    $store->shouldReceive('getPartner')->andReturn($partner);

    $this->repository->shouldReceive('find')->once()->andReturn($store);
    $this->phoneFormatter->shouldReceive('format')->andReturn('04 56 78 90 12');
    $this->djustSellerService->shouldNotReceive('getSellerLogo');

    $result = $this->provider->provide(new Get(), ['id' => 'store-id']);

    expect($result->logo)->toBe('https://cdn.example.com/accord-logo.png');
});

it('returns null logo and logs warning when djust throws', function () {
    $partnerId = Uuid::v4();

    $partner = Mockery::mock(Partner::class);
    $partner->shouldReceive('getId')->andReturn($partnerId);
    $partner->shouldReceive('getName')->andReturn('Partenaire Test');
    $partner->shouldReceive('getAccords')->andReturn(new ArrayCollection());

    $store = Mockery::mock(PartnerStore::class);
    $store->shouldReceive('getId')->andReturn(Uuid::v4());
    $store->shouldReceive('getName')->andReturn('Magasin');
    $store->shouldReceive('getAddress')->andReturn('Adresse');
    $store->shouldReceive('getPhone')->andReturn('0000000000');
    $store->shouldReceive('getLatitude')->andReturn('0');
    $store->shouldReceive('getLongitude')->andReturn('0');
    $store->shouldReceive('getPartner')->andReturn($partner);

    $this->repository->shouldReceive('find')->once()->andReturn($store);
    $this->phoneFormatter->shouldReceive('format')->andReturn('00 00 00 00 00');
    $this->currentAccountProvider->shouldReceive('getAccount')->once()->andReturn(null);
    $this->djustSellerService->shouldReceive('getSellerLogo')->andThrow(new \RuntimeException('API down'));
    $this->logger->shouldReceive('warning')->once();

    $result = $this->provider->provide(new Get(), ['id' => 'store-id']);

    expect($result->logo)->toBeNull();
});
