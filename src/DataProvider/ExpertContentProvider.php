<?php

declare(strict_types=1);

namespace App\DataProvider;

use ApiPlatform\Core\DataProvider\ContextAwareCollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Dto\ExpertContent;
use App\Service\UpplerDynamicEntityService;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ExpertContentProvider implements
    RestrictedDataProviderInterface,
    ContextAwareCollectionDataProviderInterface,
    ItemDataProviderInterface
{
    public function __construct(private UpplerDynamicEntityService $upplerDynamicEntityService, private \App\Factory\ExpertContentFactory $expertContentFactory)
    {
    }

    public function getCollection(string $resourceClass, string $operationName = null, array $context = []): array
    {
        $criteria = ['enabled' => '1', 'dynamic_entity_configuration_id' => ExpertContent::DYNAMIC_CONFIG_ID];

        $dynamicsEntities = $this->upplerDynamicEntityService->getDynamicsEntities(['dynamic_fields'], $criteria);

        return $this->expertContentFactory->createAndAddToCollection($dynamicsEntities);
    }

    public function getItem(string $resourceClass, $id, string $operationName = null, array $context = []): ExpertContent
    {
        $dynamicsEntities = $this->upplerDynamicEntityService->getDynamicsEntities(['dynamic_fields'], ['slug' => $id, 'enabled' => '1']);

        if (empty($dynamicsEntities)) {
            throw new NotFoundHttpException(\sprintf('News with slug: %s does not exist', $id));
        }

        return $this->expertContentFactory->create($dynamicsEntities[0]);
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return $resourceClass === ExpertContent::class;
    }
}
