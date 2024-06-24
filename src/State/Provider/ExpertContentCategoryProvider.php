<?php

declare(strict_types=1);

namespace App\State\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Factory\ExpertContentCategoryFactory;
use App\Service\UpplerDynamicEntityService;

readonly class ExpertContentCategoryProvider implements ProviderInterface
{
    public function __construct(private UpplerDynamicEntityService $upplerDynamicEntityService, private ExpertContentCategoryFactory $expertContentCategoryFactory)
    {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $remoteCategories = $this->upplerDynamicEntityService->getDynamicsEntitiesCategories();

        return $this->expertContentCategoryFactory->createAndAddToCollection($remoteCategories);
    }
}
