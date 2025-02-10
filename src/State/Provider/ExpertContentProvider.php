<?php

declare(strict_types=1);

namespace App\State\Provider;

use ApiPlatform\Metadata\CollectionOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Dto\ExpertContent;
use App\Factory\ExpertContentFactory;
use App\Service\UpplerDynamicEntityService;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

readonly class ExpertContentProvider implements ProviderInterface
{
    public function __construct(private UpplerDynamicEntityService $upplerDynamicEntityService, private ExpertContentFactory $expertContentFactory)
    {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        if ($operation instanceof CollectionOperationInterface) {
            $criteria = ['enabled' => '1', 'dynamic_entity_configuration_id' => ExpertContent::DYNAMIC_CONFIG_ID];

            $dynamicsEntities = $this->upplerDynamicEntityService->getDynamicsEntities(['dynamic_fields'], $criteria);

            return $this->expertContentFactory->createAndAddToCollection($dynamicsEntities);
        }

        try {
            $dynamicsEntities = $this->upplerDynamicEntityService->getDynamicsEntities(['dynamic_fields'], ['slug' => $uriVariables['slug'], 'enabled' => '1']);

            return $this->expertContentFactory->create($dynamicsEntities[0]);
        } catch (\Throwable $exception) {
            throw new NotFoundHttpException(\sprintf('News with slug: %s does not exist', $uriVariables['slug']));
        }
    }
}
