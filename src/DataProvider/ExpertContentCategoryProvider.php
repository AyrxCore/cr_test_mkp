<?php

declare(strict_types=1);

namespace App\DataProvider;

use ApiPlatform\Core\DataProvider\CollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Dto\DynamicEntity;
use App\Dto\ExpertContent;
use App\Dto\ExpertContentCategory;
use App\Dto\Product;
use App\Service\UpplerDynamicEntityService;
use App\Service\UpplerProductService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\Service\Attribute\Required;


class ExpertContentCategoryProvider implements RestrictedDataProviderInterface, ItemDataProviderInterface
    , CollectionDataProviderInterface
{
    #[Required]
    public EntityManagerInterface $em;

    #[Required]
    public UpplerDynamicEntityService $upplerDynamicEntityService;

    public function getItem(string $resourceClass, $id, string $operationName = null, array $context = [])
    {
        $expertContent = new ExpertContent();
        $expertContent->setContent('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur ac sem at enim convallis consectetur quis sed diam. Curabitur consequat sagittis tempus. Nulla mollis felis erat, non tincidunt ligula mattis vulputate. Aenean cursus dictum tempor. Proin sit amet quam in diam tempor cursus. Curabitur aliquet ut odio at vehicula. Donec tristique gravida tristique. Sed ullamcorper interdum vestibulum. Proin eu tincidunt justo.\n'
            .'\n'
            .'Curabitur turpis lectus, suscipit et velit non, ornare facilisis justo. In maximus tempor est, sodales congue dui accumsan ut. In bibendum mi nunc, ac aliquet eros placerat eu. Nunc dictum ipsum sed cursus laoreet. Vestibulum tincidunt sapien dolor, sit amet tempus purus posuere quis. Praesent tempus risus ligula, eget rhoncus velit tempus id. Fusce placerat, odio non auctor lacinia, mi libero varius diam, id sagittis ipsum tellus ac erat. Maecenas quis erat maximus, pharetra metus eget, egestas leo. Aliquam eu tortor blandit, dignissim nibh in, elementum elit.'
        );
        $expertContent->setTitle('Loi montagne : êtes-vous concernés ? ');
        $expertContent->setTeaser('Le décret tertiaire impose une réduction \nde consommation...');
        $expertContent->setId(12);
        $expertContent->setLandscapeImg('/img/1234.img');
        $expertContent->setPortraitImg('/img/1235.img');
        $expertContent->setCategorieName('Actualités');
        $expertContent->setCategorieColor('bg-secondary');

        return $expertContent;
    }

    public function getCollection(string $resourceClass, string $operationName = null)
    {
        $dynamicsFields = $this->upplerDynamicEntityService->getDynamicsEntitiesCategories();

        $names=[];
        $colors=[];
        foreach ($dynamicsFields as $dynamicField){
            if($dynamicField['name']['fr'] === 'category_name'){
               foreach($dynamicField['dynamic_field_choice'] as $choice){
                   $names[]=$choice['value'];
               }
            }elseif($dynamicField['name']['fr'] === 'category_color'){
                foreach($dynamicField['dynamic_field_choice'] as $choice){
                    $colors[]=$choice['value'];
                }
            }
        }
        dump($names);
        dump($colors);
        $categories = [];
        foreach($names as $k=>$name){
            $category = new ExpertContentCategory();
            $category->setId($k);
            $category->setName($name);
            $category->setColor($colors[$k]);
            $categories[] = $category;
        }
        dump($categories);
        return $categories;
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return ExpertContentCategory::class === $resourceClass;
    }
}
