<?php

declare(strict_types=1);

namespace App\State\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Dto\BannerSearch;
use App\Factory\BannerSearchFactory;
use App\Service\UpplerDynamicEntityService;

readonly class BannerSearchProvider implements ProviderInterface
{
    public function __construct(
        private BannerSearchFactory $bannerSearchFactory,
        private UpplerDynamicEntityService $upplerDynamicEntityService,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): array
    {
        $dynamicEntityConfigurationId = BannerSearch::DYNAMIC_CONFIG_ID;

        $entitiesBannerSearch = $this->upplerDynamicEntityService->getDynamicsEntities(
            ['dynamic_fields'],
            [],
            (string) $dynamicEntityConfigurationId
        );

        $bannersSearch = [];
        foreach ($entitiesBannerSearch as $entity) {
            $bannersSearch[] = $this->bannerSearchFactory->create($entity);
        }

        return $bannersSearch;
    }
}
