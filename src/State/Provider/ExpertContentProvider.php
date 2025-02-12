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
    public function __construct(
        private UpplerDynamicEntityService $upplerDynamicEntityService,
        private ExpertContentFactory $expertContentFactory,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        return $operation instanceof CollectionOperationInterface
            ? $this->handleCollection()
            : $this->handleSingle($uriVariables);
    }

    private function handleCollection(): array
    {
        $entities = $this->upplerDynamicEntityService->getDynamicsEntities(
            expands: ['dynamic_fields'],
            dynamicEntityConfigurationId: (string) ExpertContent::DYNAMIC_CONFIG_ID
        );

        return $this->expertContentFactory->createAndAddToCollection($entities);
    }

    private function handleSingle(array $uriVariables): object
    {
        $entities = $this->upplerDynamicEntityService->getDynamicsEntities(
            expands: ['dynamic_fields'],
            criteria: ['slug' => $uriVariables['slug']]
        );

        if (empty($entities)) {
            throw new NotFoundHttpException(
                \sprintf('News with slug: %s does not exist', $uriVariables['slug'])
            );
        }

        return $this->expertContentFactory->create($entities[0]);
    }
}
