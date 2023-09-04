<?php

declare(strict_types=1);

namespace App\DataProvider;

use ApiPlatform\Core\DataProvider\ContextAwareCollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Dto\ExpertContent;
use App\Service\UpplerDynamicEntityService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Contracts\Service\Attribute\Required;

class ExpertContentProvider implements
    RestrictedDataProviderInterface,
    ContextAwareCollectionDataProviderInterface,
    ItemDataProviderInterface
{
    #[Required]
    public EntityManagerInterface $em;

    #[Required]
    public UpplerDynamicEntityService $upplerDynamicEntityService;

    public function getCollection(string $resourceClass, string $operationName = null, array $context = []): array
    {
        $criteria = ['enabled' => '1', 'dynamic_entity_configuration_id' => ExpertContent::DYNAMIC_CONFIG_ID];

        if (\array_key_exists('filters', $context)) {
            foreach ($context['filters'] as $key => $value) {
                if ($key === 'slug') {
                    $criteria = ['slug' => $value];
                }
            }
        }

        $dynamicsEntities = $this->upplerDynamicEntityService->getDynamicsEntities(['dynamic_fields'], $criteria);
        $expertContents = [];

        foreach ($dynamicsEntities as $dynamicEntity) {
            $expertContent = new ExpertContent();
            $expertContent->hydrate($dynamicEntity);
            $expertContents[] = $expertContent;
        }

        return $expertContents;
    }

    public function getItem(string $resourceClass, $id, string $operationName = null, array $context = []): ExpertContent
    {
        $dynamicsEntities = $this->upplerDynamicEntityService->getDynamicsEntities(['dynamic_fields'], ['slug' => $id]);

        if (empty($dynamicsEntities)) {
            throw new NotFoundHttpException(\sprintf('News with slug: %s does not exist', $id));
        }

        $expertContent = new ExpertContent();
        $expertContent->hydrate($dynamicsEntities[0]);

        return $expertContent;
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return $resourceClass === ExpertContent::class;
    }
}
