<?php

declare(strict_types=1);

use App\Context\ChannelContext;
use App\Dto\Category;
use App\Entity\Channel;
use App\Factory\CategoryFactory;
use Psr\Cache\CacheItemPoolInterface;

\beforeEach(function () {
    $this->cache = Mockery::mock(CacheItemPoolInterface::class);
    $this->channelContext = Mockery::mock(ChannelContext::class);

    $this->factory = new CategoryFactory(
        $this->cache,
        $this->channelContext
    );
});

\afterEach(function () {
    Mockery::close();
});

\it('creates category with all properties', function () {
    $categoryData = [
        'id' => '123',
        'externalId' => 'ext-456',
        'name' => 'Test Category',
        'parentId' => '100',
    ];

    $result = $this->factory->create($categoryData);

    \expect($result)->toBeInstanceOf(Category::class);
    \expect($result->getId())->toBe('123');
    \expect($result->getExternalId())->toBe('ext-456');
    \expect($result->getName())->toBe('Test Category');
    \expect($result->getParentId())->toBe('100');
    \expect($result->getChildren())->toBeArray();
    \expect($result->getChildren())->toBeEmpty();
})->group('CategoryFactory', 'Djust');

\it('creates category with default values for optional fields', function () {
    $categoryData = [
        'id' => '123',
        'externalId' => 'ext-456',
        'name' => 'Test Category',
    ];

    $result = $this->factory->create($categoryData);

    \expect($result)->toBeInstanceOf(Category::class);
    \expect($result->getId())->toBe('123');
    \expect($result->getExternalId())->toBe('ext-456');
    \expect($result->getName())->toBe('Test Category');
    \expect($result->getParentId())->toBeNull();
    \expect($result->getChildren())->toBeArray();
    \expect($result->getChildren())->toBeEmpty();
})->group('CategoryFactory', 'Djust');

\it('handles name as string', function () {
    $categoryData = [
        'id' => '123',
        'externalId' => 'ext-456',
        'name' => 'Category Name',
    ];

    $result = $this->factory->create($categoryData);

    \expect($result)->toBeInstanceOf(Category::class);
    \expect($result->getName())->toBe('Category Name');
})->group('CategoryFactory', 'Djust');

\it('creates category with children recursively', function () {
    $categoryData = [
        'id' => '123',
        'externalId' => 'ext-456',
        'name' => 'Parent Category',
        'childrenCategories' => [
            [
                'id' => '124',
                'externalId' => 'ext-457',
                'name' => 'Child Category 1',
            ],
            [
                'id' => '125',
                'externalId' => 'ext-458',
                'name' => 'Child Category 2',
                'childrenCategories' => [
                    [
                        'id' => '126',
                        'externalId' => 'ext-459',
                        'name' => 'Nested Child',
                    ],
                ],
            ],
        ],
    ];

    $result = $this->factory->create($categoryData);

    \expect($result)->toBeInstanceOf(Category::class);
    \expect($result->getName())->toBe('Parent Category');
    \expect($result->getChildren())->toBeArray();
    \expect($result->getChildren())->toHaveCount(2);
    \expect($result->getChildren()[0])->toBeInstanceOf(Category::class);
    \expect($result->getChildren()[0]->getName())->toBe('Child Category 1');
    \expect($result->getChildren()[1])->toBeInstanceOf(Category::class);
    \expect($result->getChildren()[1]->getName())->toBe('Child Category 2');

    // Verify nested children
    \expect($result->getChildren()[1]->getChildren())->toHaveCount(1);
    \expect($result->getChildren()[1]->getChildren()[0]->getName())->toBe('Nested Child');
})->group('CategoryFactory', 'Djust');

\it('uses custom category name from channel option', function () {
    $channel = Mockery::mock(Channel::class);
    $channel->shouldReceive('getChannelOptionValueByKey')
        ->with('CUSTOM_CATEGORIES_NAME')
        ->andReturn(\json_encode(['123' => 'Custom Category Name']));

    $this->channelContext->shouldReceive('getChannel')->andReturn($channel);

    $categoryData = [
        [
            'id' => '123',
            'externalId' => 'ext-456',
            'name' => 'Original Name',
        ],
    ];

    $result = $this->factory->createAndAddToCollection($categoryData);

    \expect($result)->toBeArray();
    \expect($result)->toHaveCount(1);
    \expect($result[0])->toBeInstanceOf(Category::class);
    \expect($result[0]->getName())->toBe('Custom Category Name');
})->group('CategoryFactory', 'Djust');

\it('handles null custom category name from channel option', function () {
    $channel = Mockery::mock(Channel::class);
    $channel->shouldReceive('getChannelOptionValueByKey')
        ->with('CUSTOM_CATEGORIES_NAME')
        ->andReturn(null);

    $this->channelContext->shouldReceive('getChannel')->andReturn($channel);

    $categoryData = [
        [
            'id' => '123',
            'externalId' => 'ext-456',
            'name' => 'Original Name',
        ],
    ];

    $result = $this->factory->createAndAddToCollection($categoryData);

    \expect($result)->toBeArray();
    \expect($result)->toHaveCount(1);
    \expect($result[0])->toBeInstanceOf(Category::class);
    \expect($result[0]->getName())->toBe('Original Name');
})->group('CategoryFactory', 'Djust');

\it('creates multiple categories via createAndAddToCollection', function () {
    $channel = Mockery::mock(Channel::class);
    $channel->shouldReceive('getChannelOptionValueByKey')->andReturn(null);
    $this->channelContext->shouldReceive('getChannel')->andReturn($channel);

    $categoriesData = [
        [
            'id' => '123',
            'externalId' => 'ext-456',
            'name' => 'Category 1',
        ],
        [
            'id' => '124',
            'externalId' => 'ext-457',
            'name' => 'Category 2',
        ],
        [
            'id' => '125',
            'externalId' => 'ext-458',
            'name' => 'Category 3',
        ],
    ];

    $result = $this->factory->createAndAddToCollection($categoriesData);

    \expect($result)->toBeArray();
    \expect($result)->toHaveCount(3);
    \expect($result[0]->getName())->toBe('Category 1');
    \expect($result[1]->getName())->toBe('Category 2');
    \expect($result[2]->getName())->toBe('Category 3');
})->group('CategoryFactory', 'Djust');

\it('preserves parentId from input data', function () {
    $categoryData = [
        'id' => '123',
        'externalId' => 'ext-456',
        'name' => 'Child Category',
        'parentId' => 'parent-id-from-service',
    ];

    $result = $this->factory->create($categoryData);

    \expect($result)->toBeInstanceOf(Category::class);
    \expect($result->getParentId())->toBe('parent-id-from-service');
})->group('CategoryFactory', 'Djust');
