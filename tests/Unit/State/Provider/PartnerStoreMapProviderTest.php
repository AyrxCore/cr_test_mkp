<?php

declare(strict_types=1);

use ApiPlatform\Metadata\Get;
use App\Dto\MapStoreDataDto;
use App\Entity\Partner;
use App\Factory\CategoryFactory;
use App\Mapper\DjustSearchParamsMapper;
use App\Repository\PartnerRepository;
use App\Service\Djust\DjustCategoryService;
use App\Service\Djust\Search\DjustSearchService;
use App\Dto\Djust\DjustSearchParams;
use App\Service\MapStoreBuilderService;
use Doctrine\Common\Collections\ArrayCollection;
use App\State\Provider\PartnerStoreMapProvider;
use Psr\Log\LoggerInterface;
use Symfony\Component\Uid\Uuid;

uses()->group('UnitPartnerStoreMapProvider');

beforeEach(function () {
    $this->categoryFactory = Mockery::mock(CategoryFactory::class);
    $this->djustCategoryService = Mockery::mock(DjustCategoryService::class);
    $this->djustSearchParamsMapper = Mockery::mock(DjustSearchParamsMapper::class);
    $this->djustSearchService = Mockery::mock(DjustSearchService::class);
    $this->logger = Mockery::mock(LoggerInterface::class);
    $this->mapStoreBuilderService = new MapStoreBuilderService();
    $this->partnerRepository = Mockery::mock(PartnerRepository::class);

    $this->provider = new PartnerStoreMapProvider(
        $this->categoryFactory,
        $this->djustCategoryService,
        $this->djustSearchParamsMapper,
        $this->djustSearchService,
        $this->logger,
        $this->mapStoreBuilderService,
        $this->partnerRepository,
    );
});

afterEach(function () {
    Mockery::close();
});

it('returns empty response when no partners found', function () {
    $this->djustSearchParamsMapper->shouldReceive('fromContext')->andReturn(new DjustSearchParams());
    $this->djustSearchService->shouldReceive('search')->andReturn(['facets' => ['suppliers' => []]]);
    $this->partnerRepository->shouldReceive('findByDjustIds')->andReturn([]);

    $result = $this->provider->provide(new Get());

    expect($result)->toBeInstanceOf(MapStoreDataDto::class)
        ->and($result->stores)->toBe([])
        ->and($result->categories)->toBe([['id' => 'all', 'name' => 'Toutes les catégories']]);
});

it('returns stores and categories when partners are found', function () {
    $uuid = Uuid::fromString('4c4f6064-5827-11ec-999d-028bf10cd626');

    $partner = Mockery::mock(Partner::class);
    $partner->shouldReceive('getId')->andReturn($uuid);

    $params = new DjustSearchParams();
    $this->djustSearchParamsMapper->shouldReceive('fromContext')->andReturn($params);
    $this->djustSearchService->shouldReceive('search')->andReturn([
        'facets' => ['suppliers' => [['externalId' => '4c4f6064-5827-11ec-999d-028bf10cd626']]],
    ]);
    $this->partnerRepository->shouldReceive('findByDjustIds')->andReturn([$partner]);
    $partner->shouldReceive('getPartnerStores')->andReturn(new ArrayCollection([]));

    $this->djustCategoryService->shouldReceive('getAvailableCategories')->andReturn([]);
    $this->categoryFactory->shouldReceive('createAndAddToCollection')->andReturn([['id' => 'all', 'name' => 'Toutes les catégories']]);

    $result = $this->provider->provide(new Get());

    expect($result)->toBeInstanceOf(MapStoreDataDto::class)
        ->and($result->stores)->toBe([]);
});

it('returns empty response and logs error on exception', function () {
    $this->djustSearchParamsMapper->shouldReceive('fromContext')->andThrow(new \RuntimeException('Service down'));
    $this->logger->shouldReceive('error')->once();

    $result = $this->provider->provide(new Get());

    expect($result)->toBeInstanceOf(MapStoreDataDto::class)
        ->and($result->stores)->toBe([]);
});
