<?php

declare(strict_types=1);

namespace App\DataProvider;

use ApiPlatform\Core\DataProvider\CollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\ContextAwareCollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;

//use App\Dto\DynamicEntity;
use App\Dto\ExpertContent;
use App\Dto\Product;
use App\Service\UpplerDynamicEntityService;
use App\Service\UpplerProductService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\Service\Attribute\Required;


class ExpertContentProvider implements RestrictedDataProviderInterface
    , ContextAwareCollectionDataProviderInterface
{

    #[Required]
    public EntityManagerInterface $em;

    #[Required]
    public UpplerDynamicEntityService $upplerDynamicEntityService;


    public function getCollection(string $resourceClass, string $operationName = null, array $context = [])
    {
        $filters = ['enabled' => '1'];
        if (key_exists('filters', $context)) {
            foreach ($context['filters'] as $filter => $value) {
                if ($filter === 'slug') {
                    $filters = ['slug' => $value];
                }
            }
        }

        $dynamicsEntities = $this->upplerDynamicEntityService->getDynamicsEntities(['dynamic_fields'], $filters);
        $expertsContents = [];

        foreach ($dynamicsEntities as $dynamicEntity) {
            $expertContent = new ExpertContent();
            $expertContent->hydrate($dynamicEntity);
            $expertsContents[] = $expertContent;
        }

        return $expertsContents;
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return ExpertContent::class === $resourceClass;
    }

}
