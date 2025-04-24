<?php

declare(strict_types=1);

namespace App\State\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Dto\BannerSearch;
use App\Factory\BannerSearchFactory;
use App\Service\UpplerDynamicEntityService;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

readonly class BannerSearchProvider implements ProviderInterface
{
    public function __construct(
        private BannerSearchFactory $bannerSearchFactory,
        private UpplerDynamicEntityService $upplerDynamicEntityService,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $dynamicEntityConfigurationId = BannerSearch::DYNAMIC_CONFIG_ID;

        $entitiesBannerSearch = $this->upplerDynamicEntityService->getDynamicsEntities(
            ['dynamic_fields'],
            [],
            (string) $dynamicEntityConfigurationId
        );

        if (\count($entitiesBannerSearch)) {
            $bannersSearch = [];
            foreach ($entitiesBannerSearch as $entity) {
                $bannersSearch[] = $this->bannerSearchFactory->create($entity);
            }

            return $bannersSearch;
        }

        throw new NotFoundHttpException('Not Found');
    }
}
