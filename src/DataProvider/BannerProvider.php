<?php

declare(strict_types=1);

namespace App\DataProvider;

use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Dto\Banner;
use App\Service\UpplerDynamicEntityService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Contracts\Service\Attribute\Required;

class BannerProvider implements RestrictedDataProviderInterface, ItemDataProviderInterface
{
    #[Required]
    public EntityManagerInterface $em;

    #[Required]
    public UpplerDynamicEntityService $upplerDynamicEntityService;

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return $resourceClass === Banner::class;
    }

    public function getItem(string $resourceClass, $id, string $operationName = null, array $context = []): Banner
    {
        $entitiesBanner = $this->upplerDynamicEntityService->getDynamicsEntities(['dynamic_fields'], ['dynamic_entity_configuration_id' => Banner::DYNAMIC_CONFIG_ID, 'enabled' => 1]);

        if (!empty($entitiesBanner[0])) {
            $banner = new Banner();
            $banner->setId($entitiesBanner[0]['id']);
            $banner->setSlug($entitiesBanner[0]['slug']);
            foreach ($entitiesBanner[0]['dynamic_fields'] as $value) {
                $fieldName = $value['dynamic_field_configuration']['name']['default'];
                $fieldValue = $value['value'];
                switch ($fieldName) {
                    case 'bandeau_flash_text':
                        $banner->setText($fieldValue);
                        break;
                    case 'bandeau_flash_cta_text':
                        $banner->setCtaTxt($fieldValue);
                        break;
                    case 'bandeau_flash_cta_link':
                        $banner->setCtaLink($fieldValue);
                        break;
                }
            }

            return $banner;
        } else {
            throw new NotFoundHttpException('Not Found');
        }
    }
}
