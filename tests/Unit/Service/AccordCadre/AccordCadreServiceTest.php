<?php

declare(strict_types=1);

use App\Dto\AccordCadre\AccordCadreContent;
use App\Dto\AccordCadre\ListBlocks\BannerBlockContent;
use App\Dto\AccordCadre\ListBlocks\PresentationBlockContent;
use App\Enum\Storyblok\StoryblokEndpoint;
use App\Service\AccordCadre\AccordCadreService;
use App\Service\AccordCadre\StoryblokToAccordCadreMapper;
use App\Service\Storyblok\StoryblokHttpClient;
use Symfony\Component\Cache\Adapter\ArrayAdapter;

\uses()->group('UnitAccordCadreService');

\beforeEach(function () {
    $this->httpClient = Mockery::mock(StoryblokHttpClient::class);
    $this->mapper = Mockery::mock(StoryblokToAccordCadreMapper::class);
    $this->cache = new ArrayAdapter();

    $this->service = new AccordCadreService(
        $this->httpClient,
        $this->mapper,
        $this->cache
    );
});

\afterEach(function () {
    Mockery::close();
});

\it('returns all accord cadres when cache is not hit', function () {
    $rawData = [
        'stories' => [
            ['content' => ['tarifId' => 'tarif-1']],
            ['content' => ['tarifId' => 'tarif-2']],
        ],
    ];

    $accordCadre1 = new AccordCadreContent();
    $accordCadre1->setTarifId('tarif-1');
    $block1 = new BannerBlockContent();
    $block1->setComponentName('bannerBlock');
    $accordCadre1->addListBlock($block1);

    $accordCadre2 = new AccordCadreContent();
    $accordCadre2->setTarifId('tarif-2');
    $block2 = new PresentationBlockContent();
    $block2->setComponentName('presentationBlock');
    $accordCadre2->addListBlock($block2);

    $accordCadres = [$accordCadre1, $accordCadre2];

    $this->httpClient
        ->shouldReceive('getStories')
        ->once()
        ->with(Mockery::on(function ($args) {
            return $args['starts_with'] === StoryblokEndpoint::ACCORD_CADRE->value
                && $args['sort_by'] === 'first_published_at:desc';
        }))
        ->andReturn($rawData);

    $this->mapper
        ->shouldReceive('mapStories')
        ->once()
        ->with($rawData)
        ->andReturn($accordCadres);

    $result = $this->service->getAll();

    \expect($result)->toBeArray()
        ->and($result)->toHaveCount(2)
        ->and($result[0])->toBeInstanceOf(AccordCadreContent::class)
        ->and($result[0]->getTarifId())->toBe('tarif-1')
        ->and($result[1])->toBeInstanceOf(AccordCadreContent::class)
        ->and($result[1]->getTarifId())->toBe('tarif-2');
});

\it('filters out accord cadres with no list blocks', function () {
    $rawData = [
        'stories' => [
            ['content' => ['tarifId' => 'tarif-1']],
            ['content' => ['tarifId' => 'tarif-2']],
        ],
    ];

    $accordCadre1 = new AccordCadreContent();
    $accordCadre1->setTarifId('tarif-1');
    $block1 = new BannerBlockContent();
    $block1->setComponentName('bannerBlock');
    $accordCadre1->addListBlock($block1);

    $accordCadre2 = new AccordCadreContent();
    $accordCadre2->setTarifId('tarif-2');

    $accordCadres = [$accordCadre1, $accordCadre2];

    $this->httpClient
        ->shouldReceive('getStories')
        ->once()
        ->andReturn($rawData);

    $this->mapper
        ->shouldReceive('mapStories')
        ->once()
        ->andReturn($accordCadres);

    $result = $this->service->getAll();

    \expect($result)->toBeArray()
        ->and($result)->toHaveCount(1)
        ->and($result[0]->getTarifId())->toBe('tarif-1');
});

\it('returns cached accord cadre by id when cache is hit', function () {
    $tarifId = 'tarif-123';

    $accordCadre1 = new AccordCadreContent();
    $accordCadre1->setTarifId('tarif-123');
    $block1 = new BannerBlockContent();
    $block1->setComponentName('bannerBlock');
    $accordCadre1->addListBlock($block1);

    $accordCadre2 = new AccordCadreContent();
    $accordCadre2->setTarifId('tarif-456');
    $block2 = new PresentationBlockContent();
    $block2->setComponentName('presentationBlock');
    $accordCadre2->addListBlock($block2);

    $cachedAccordCadres = [$accordCadre1, $accordCadre2];

    $cacheItem = $this->cache->getItem('storyblok_accord_cadre_content');
    $cacheItem->set($cachedAccordCadres);
    $cacheItem->expiresAfter(300);
    $this->cache->save($cacheItem);

    $result = $this->service->getAccordCadreContentByTarifId($tarifId);

    \expect($result)->toBeInstanceOf(AccordCadreContent::class)
        ->and($result->getTarifId())->toBe($tarifId);
});

\it('returns null when accord cadre id is not found in cache', function () {
    $tarifId = 'tarif-non-existant';

    $accordCadre1 = new AccordCadreContent();
    $accordCadre1->setTarifId('tarif-123');
    $block1 = new BannerBlockContent();
    $block1->setComponentName('bannerBlock');
    $accordCadre1->addListBlock($block1);

    $cachedAccordCadres = [$accordCadre1];

    $cacheItem = $this->cache->getItem('storyblok_accord_cadre_content');
    $cacheItem->set($cachedAccordCadres);
    $cacheItem->expiresAfter(300);
    $this->cache->save($cacheItem);

    $result = $this->service->getAccordCadreContentByTarifId($tarifId);

    \expect($result)->toBeNull();
});

\it('fetches and caches accord cadres when cache is not hit', function () {
    $tarifId = 'tarif-123';
    $rawData = [
        'stories' => [
            ['content' => ['tarifId' => 'tarif-123']],
        ],
    ];

    $accordCadre = new AccordCadreContent();
    $accordCadre->setTarifId('tarif-123');
    $block = new BannerBlockContent();
    $block->setComponentName('bannerBlock');
    $accordCadre->addListBlock($block);

    $accordCadres = [$accordCadre];

    $this->httpClient
        ->shouldReceive('getStories')
        ->once()
        ->andReturn($rawData);

    $this->mapper
        ->shouldReceive('mapStories')
        ->once()
        ->andReturn($accordCadres);

    $result = $this->service->getAccordCadreContentByTarifId($tarifId);

    \expect($result)->toBeInstanceOf(AccordCadreContent::class)
        ->and($result->getTarifId())->toBe($tarifId);

    $cacheItem = $this->cache->getItem('storyblok_accord_cadre_content');
    \expect($cacheItem->isHit())->toBeTrue();
});

\it('returns empty array when no stories are returned from storyblok', function () {
    $rawData = ['stories' => []];

    $this->httpClient
        ->shouldReceive('getStories')
        ->once()
        ->andReturn($rawData);

    $this->mapper
        ->shouldReceive('mapStories')
        ->once()
        ->with($rawData)
        ->andReturn([]);

    $result = $this->service->getAll();

    \expect($result)->toBeArray()
        ->and($result)->toHaveCount(0);
});

\it('caches accord cadres with correct expiration time', function () {
    $tarifId = 'tarif-123';
    $rawData = ['stories' => []];

    $this->httpClient
        ->shouldReceive('getStories')
        ->once()
        ->andReturn($rawData);

    $this->mapper
        ->shouldReceive('mapStories')
        ->once()
        ->andReturn([]);

    $result = $this->service->getAccordCadreContentByTarifId($tarifId);

    \expect($result)->toBeNull();

    $cacheItem = $this->cache->getItem('storyblok_accord_cadre_content');
    \expect($cacheItem->isHit())->toBeTrue()
        ->and($cacheItem->get())->toBe([]);
});

\it('returns map of tarifIds from all accord cadres', function () {
    $rawData = [
        'stories' => [
            ['content' => ['tarifId' => 'tarif-123']],
            ['content' => ['tarifId' => 'tarif-456']],
        ],
    ];

    $accordCadre1 = new AccordCadreContent();
    $accordCadre1->setTarifId('tarif-123');
    $block1 = new BannerBlockContent();
    $block1->setComponentName('bannerBlock');
    $accordCadre1->addListBlock($block1);

    $accordCadre2 = new AccordCadreContent();
    $accordCadre2->setTarifId('tarif-456');
    $block2 = new PresentationBlockContent();
    $block2->setComponentName('presentationBlock');
    $accordCadre2->addListBlock($block2);

    $accordCadres = [$accordCadre1, $accordCadre2];

    $this->httpClient
        ->shouldReceive('getStories')
        ->once()
        ->andReturn($rawData);

    $this->mapper
        ->shouldReceive('mapStories')
        ->once()
        ->with($rawData)
        ->andReturn($accordCadres);

    $result = $this->service->getTarifIds();

    \expect($result)->toBe([
        'tarif-123' => true,
        'tarif-456' => true,
    ]);
});

\it('skips accord cadres with empty tarifId in getTarifIds', function () {
    $rawData = [
        'stories' => [
            ['content' => ['tarifId' => 'tarif-123']],
            ['content' => ['tarifId' => '']],
        ],
    ];

    $accordCadre1 = new AccordCadreContent();
    $accordCadre1->setTarifId('tarif-123');
    $block1 = new BannerBlockContent();
    $block1->setComponentName('bannerBlock');
    $accordCadre1->addListBlock($block1);

    $accordCadre2 = new AccordCadreContent();
    $accordCadre2->setTarifId('');
    $block2 = new BannerBlockContent();
    $block2->setComponentName('bannerBlock');
    $accordCadre2->addListBlock($block2);

    $accordCadres = [$accordCadre1, $accordCadre2];

    $this->httpClient
        ->shouldReceive('getStories')
        ->once()
        ->andReturn($rawData);

    $this->mapper
        ->shouldReceive('mapStories')
        ->once()
        ->with($rawData)
        ->andReturn($accordCadres);

    $result = $this->service->getTarifIds();

    \expect($result)->toBe(['tarif-123' => true]);
});

