<?php

declare(strict_types=1);

namespace App\DataProvider;

use ApiPlatform\Core\DataProvider\CollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Dto\Category;
use App\Factory\CategoryFactory;
use App\Service\UpplerProductService;

class CategoryDataProvider implements RestrictedDataProviderInterface, CollectionDataProviderInterface
{
    public function __construct(private UpplerProductService $upplerProductService, private CategoryFactory $categoryFactory)
    {
    }

    public function getCollection(string $resourceClass, string $operationName = null): array
    {
        return $this->categoryFactory->createAndAddToCollection($this->upplerProductService->findAllCategories());
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return $resourceClass === Category::class;
    }
}
