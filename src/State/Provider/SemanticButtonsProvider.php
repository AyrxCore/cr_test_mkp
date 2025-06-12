<?php

declare(strict_types=1);

namespace App\State\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Context\ChannelContext;
use App\Dto\SemanticButton;
use App\Factory\SemanticButtonFactory;
use App\Service\UpplerDynamicEntityService;

readonly class SemanticButtonsProvider implements ProviderInterface
{
    public function __construct(
        private SemanticButtonFactory $semanticButtonFactory,
        private UpplerDynamicEntityService $upplerDynamicEntityService,
        private ChannelContext $channelContext,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $entitiesSemanticButton = $this->upplerDynamicEntityService->getDynamicsEntities(
            ['dynamic_fields'],
            [],
            (string) SemanticButton::DYNAMIC_CONFIG_ID
        );

        $semanticButtons = $this->semanticButtonFactory->createAndAddToCollection($entitiesSemanticButton);

        return \array_values(\array_filter($semanticButtons, function ($semanticButton) {
            return $semanticButton->getChannel() === $this->channelContext->getChannel()->getCode();
        }));
    }
}
