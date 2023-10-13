<?php

declare(strict_types=1);

namespace App\DataProvider;

use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Dto\Banner;
use App\Service\UpplerDynamicEntityService;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class BannerProvider implements RestrictedDataProviderInterface, ItemDataProviderInterface
{
    public function __construct(private UpplerDynamicEntityService $upplerDynamicEntityService, private \App\Factory\BannerFactory $bannerFactory)
    {
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return $resourceClass === Banner::class;
    }

    public function getItem(string $resourceClass, $id, string $operationName = null, array $context = []): Banner
    {
        $entitiesBanner = $this->upplerDynamicEntityService->getDynamicsEntities(
            ['dynamic_fields'],
            ['dynamic_entity_configuration_id' => Banner::DYNAMIC_CONFIG_ID, 'enabled' => 1]
        );

        if (!empty($entitiesBanner[0])) {
            return $this->bannerFactory->create($entitiesBanner[0]);
        }

        throw new NotFoundHttpException('Not Found');
    }
}
