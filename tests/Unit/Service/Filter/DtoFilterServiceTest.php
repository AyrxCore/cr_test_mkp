<?php

declare(strict_types=1);

use App\Filter\DtoFilterInterface;
use App\Filter\FilterableInterface;
use App\Service\Filter\DtoFilterService;
use Psr\Log\LoggerInterface;

beforeEach(function () {
    $this->logger = mock(LoggerInterface::class);
    $this->filterService = new DtoFilterService([], $this->logger);
    
    $this->createMockFilterable = function (array $criteria): FilterableInterface {
        $mock = mock(FilterableInterface::class);
        $mock->shouldReceive('getFilterCriteria')->andReturn($criteria);

        return $mock;
    };
});

it('filter with no filters returns all objects', function () {
    $objects = [
        ($this->createMockFilterable)(['tags' => ['premium']]),
        ($this->createMockFilterable)(['tags' => ['basic']]),
    ];

    $result = $this->filterService->filter($objects);

    expect($result)->toHaveCount(2);
});

it('filter with allowing filter returns matching objects', function () {
    $filter = mock(DtoFilterInterface::class);
    $filter->shouldReceive('shouldInclude')
        ->andReturnUsing(function (FilterableInterface $object) {
            $criteria = $object->getFilterCriteria();
            return !isset($criteria['tags']) || \in_array('premium', $criteria['tags'], true);
        });
    $filter->shouldReceive('getName')->andReturn('test_filter');

    $this->filterService = new DtoFilterService([$filter], $this->logger);

    $objects = [
        ($this->createMockFilterable)(['tags' => ['premium']]),
        ($this->createMockFilterable)(['tags' => ['basic']]),
        ($this->createMockFilterable)([]),
    ];

    $result = $this->filterService->filter($objects);

    expect($result)->toHaveCount(2);
});

it('shouldInclude with no filters returns true', function () {
    $object = ($this->createMockFilterable)(['tags' => ['premium']]);

    $result = $this->filterService->shouldInclude($object);

    expect($result)->toBeTrue();
});

it('shouldInclude with denying filter returns false', function () {
    $filter = mock(DtoFilterInterface::class);
    $filter->shouldReceive('shouldInclude')->andReturn(false);
    $filter->shouldReceive('getName')->andReturn('denying_filter');

    $this->filterService = new DtoFilterService([$filter], $this->logger);

    $object = ($this->createMockFilterable)(['tags' => ['premium']]);

    $result = $this->filterService->shouldInclude($object);

    expect($result)->toBeFalse();
});
