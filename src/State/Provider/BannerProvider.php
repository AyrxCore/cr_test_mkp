<?php

declare(strict_types=1);

namespace App\State\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Dto\Banner;
use App\Factory\BannerFactory;
use App\Service\UpplerDynamicEntityService;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

readonly class BannerProvider implements ProviderInterface
{
    public function __construct(
        private BannerFactory $bannerFactory,
        private UpplerDynamicEntityService $upplerDynamicEntityService
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $dynamicEntityConfigurationId = Banner::DYNAMIC_CONFIG_ID;

        $entitiesBanner = $this->upplerDynamicEntityService->getDynamicsEntities(
            ['dynamic_fields'],
            [],
            (string) $dynamicEntityConfigurationId
        );

        if (!empty($entitiesBanner[0])) {
            return $this->bannerFactory->create($entitiesBanner[0]);
        }

        throw new NotFoundHttpException('Not Found');
    }
}
