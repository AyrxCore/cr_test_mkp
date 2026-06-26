<?php

declare(strict_types=1);

namespace App\Factory;

use App\Context\ChannelContext;
use App\Dto\Category;
use Psr\Cache\CacheItemPoolInterface as AdapterInterface;

class CategoryFactory extends AbstractFactory
{
    private const CUSTOM_CATEGORY_NAME_KEY = 'CUSTOM_CATEGORIES_NAME';
    private array $customCategoriesValues = [];

    public function __construct(
        protected AdapterInterface $cache,
        private readonly ChannelContext $channelContext,
    ) {
        parent::__construct($this->cache);
    }

    public function createAndAddToCollection(array $data): array
    {
        $value = $this->channelContext->getChannel()->getChannelOptionValueByKey(self::CUSTOM_CATEGORY_NAME_KEY);

        $this->customCategoriesValues = $value ? \json_decode($value, true) : [];

        return parent::createAndAddToCollection($data);
    }

    public function create(array $data): Category
    {
        $category = new Category();
        $category->setId((string) $data['id']);
        $category->setExternalId($data['externalId'] ?? null);
        $category->setName(
            $this->customCategoriesValues[$data['id']] ?? $data['name']
        );
        $category->setParentId(isset($data['parentId']) ? (string) $data['parentId'] : null);
        $children = [];

        if (!empty($data['childrenCategories'])) {
            foreach ($data['childrenCategories'] as $childData) {
                $children[] = $this->create($childData);
            }
        }

        $category->setChildren($children);

        return $category;
    }
}
