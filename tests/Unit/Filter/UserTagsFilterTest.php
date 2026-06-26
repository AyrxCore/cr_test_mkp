<?php

declare(strict_types=1);

use App\Filter\UserTagsFilter;
use App\Service\Djust\DjustCustomerAccountService;

\beforeEach(function () {
    $this->djustService = Mockery::mock(DjustCustomerAccountService::class);
    $this->filter = new UserTagsFilter($this->djustService);
});

\afterEach(function () {
    Mockery::close();
});

\it('returns correct filter name', function () {
    \expect($this->filter->getName())->toBe('user_tags_filter');
})->group('UserTagsFilter', 'filter', 'djust');

\it('returns false when no tags are required', function () {
    $object = \createMockFilterable([]);

    $this->djustService->shouldNotReceive('getUserTags');

    $result = $this->filter->shouldInclude($object);

    \expect($result)->toBeFalse();
})->group('UserTagsFilter', 'filter', 'djust');

\it('returns false when user has no tags', function () {
    $object = \createMockFilterable(['tags' => ['premium-uuid']]);

    $this->djustService->shouldReceive('getUserTags')->andReturn([]);

    $result = $this->filter->shouldInclude($object);

    \expect($result)->toBeFalse();
})->group('UserTagsFilter', 'filter', 'djust');

\it('returns true when user has required tag', function () {
    $object = \createMockFilterable(['tags' => ['premium-uuid']]);

    $this->djustService->shouldReceive('getUserTags')->andReturn(['vip-uuid', 'premium-uuid', 'gold-uuid']);

    $result = $this->filter->shouldInclude($object);

    \expect($result)->toBeTrue();
})->group('UserTagsFilter', 'filter', 'djust');

\it('returns false when user does not have required tag', function () {
    $object = \createMockFilterable(['tags' => ['premium-uuid']]);

    $this->djustService->shouldReceive('getUserTags')->andReturn(['vip-uuid', 'basic-uuid']);

    $result = $this->filter->shouldInclude($object);

    \expect($result)->toBeFalse();
})->group('UserTagsFilter', 'filter', 'djust');

\it('returns true when user has all required tags', function () {
    $object = \createMockFilterable(['tags' => ['premium-uuid', 'vip-uuid']]);

    $this->djustService->shouldReceive('getUserTags')->andReturn(['vip-uuid', 'premium-uuid', 'gold-uuid']);

    $result = $this->filter->shouldInclude($object);

    \expect($result)->toBeTrue();
})->group('UserTagsFilter', 'filter', 'djust');

\it('returns true when user has at least one required tag', function () {
    $object = \createMockFilterable(['tags' => ['premium-uuid', 'vip-uuid', 'platinum-uuid']]);

    $this->djustService->shouldReceive('getUserTags')->andReturn(['vip-uuid', 'premium-uuid']);

    $result = $this->filter->shouldInclude($object);

    \expect($result)->toBeTrue();
})->group('UserTagsFilter', 'filter', 'djust');

\it('returns false when user has none of required tags', function () {
    $object = \createMockFilterable(['tags' => ['premium-uuid', 'vip-uuid', 'platinum-uuid']]);

    $this->djustService->shouldReceive('getUserTags')->andReturn(['basic-uuid', 'standard-uuid']);

    $result = $this->filter->shouldInclude($object);

    \expect($result)->toBeFalse();
})->group('UserTagsFilter', 'filter', 'djust');

function createMockFilterable(array $criteria): App\Filter\FilterableInterface
{
    $mock = Mockery::mock(\App\Filter\FilterableInterface::class);
    $mock->shouldReceive('getFilterCriteria')->andReturn($criteria);

    return $mock;
}
