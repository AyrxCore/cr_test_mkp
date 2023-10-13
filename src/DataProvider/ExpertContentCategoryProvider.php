<?php

declare(strict_types=1);

namespace App\DataProvider;

use ApiPlatform\Core\DataProvider\CollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Dto\ExpertContentCategory;
use App\Factory\ExpertContentCategoryFactory;
use App\Service\UpplerDynamicEntityService;

class ExpertContentCategoryProvider implements
    RestrictedDataProviderInterface,
    CollectionDataProviderInterface
{
    public function __construct(private UpplerDynamicEntityService $upplerDynamicEntityService, private ExpertContentCategoryFactory $expertContentCategoryFactory)
    {
    }

    public function getCollection(string $resourceClass, string $operationName = null): array
    {
        $remoteCategories = $this->upplerDynamicEntityService->getDynamicsEntitiesCategories();

        return $this->expertContentCategoryFactory->createAndAddToCollection($remoteCategories);
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return $resourceClass === ExpertContentCategory::class;
    }
}
