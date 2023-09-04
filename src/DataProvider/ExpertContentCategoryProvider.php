<?php

declare(strict_types=1);

namespace App\DataProvider;

use ApiPlatform\Core\DataProvider\CollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Dto\ExpertContentCategory;
use App\Service\UpplerDynamicEntityService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Service\Attribute\Required;

class ExpertContentCategoryProvider implements
    RestrictedDataProviderInterface,
    CollectionDataProviderInterface
{
    #[Required]
    public EntityManagerInterface $em;

    #[Required]
    public UpplerDynamicEntityService $upplerDynamicEntityService;

    public function getCollection(string $resourceClass, string $operationName = null): array
    {
        $dynamicsFields = $this->upplerDynamicEntityService->getDynamicsEntitiesCategories();

        $names = [];
        $colors = [];
        foreach ($dynamicsFields as $dynamicField) {
            if ($dynamicField['name']['fr'] === 'category_name') {
                foreach ($dynamicField['dynamic_field_choice'] as $choice) {
                    $names[] = $choice['value'];
                }
            } elseif ($dynamicField['name']['fr'] === 'category_color') {
                foreach ($dynamicField['dynamic_field_choice'] as $choice) {
                    $colors[] = $choice['value'];
                }
            }
        }

        $categories = [];
        foreach ($names as $k => $name) {
            $category = new ExpertContentCategory();
            $category->setId($k);
            $category->setName($name);
            $category->setColor($colors[$k]);
            $categories[] = $category;
        }

        return $categories;
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return $resourceClass === ExpertContentCategory::class;
    }
}
