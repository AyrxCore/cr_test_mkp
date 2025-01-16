<?php

declare(strict_types=1);

namespace App\State\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Context\ChannelContext;
use App\Factory\SemanticButtonFactory;
use App\Service\UpplerDynamicEntityService;

readonly class SemanticButtonsProvider implements ProviderInterface
{
    private const string CUSTOM_CATEGORY_NAME_KEY = 'SEMANTIC_BUTTONS_HOMEPAGE';

    public function __construct(
        private SemanticButtonFactory $semanticButtonFactory,
        private UpplerDynamicEntityService $upplerDynamicEntityService,
        private ChannelContext $channelContext,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $channel = $this->channelContext->getChannel();

        $dynamicConfigId = $channel->getChannelOptionValueByKey(self::CUSTOM_CATEGORY_NAME_KEY);

        if (!$dynamicConfigId) {
            return [];
        } else {
            $entitiesSemanticButton = $this->upplerDynamicEntityService->getDynamicsEntities(
                ['dynamic_fields'],
                ['dynamic_entity_configuration_id' => $dynamicConfigId, 'enabled' => 1]
            );

            return $this->semanticButtonFactory->createAndAddToCollection($entitiesSemanticButton);
        }
    }
}
