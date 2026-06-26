<?php

declare(strict_types=1);

use ApiPlatform\Metadata\Operation;
use App\Context\ChannelContext;
use App\DataFixtures\Factory\ChannelFactory;
use App\Factory\SemanticButtonFactory;
use App\Service\UpplerDynamicEntityService;
use App\State\Provider\SemanticButtonsProvider;

\it('returns semantic buttons array', function () {
    $semanticButtonFactory = $this->container->get(SemanticButtonFactory::class);
    $upplerDynamicEntityService = Mockery::mock(UpplerDynamicEntityService::class);
    $upplerDynamicEntityService->shouldReceive('getDynamicsEntities')->andReturn([]);

    $channel = ChannelFactory::createOne([
        'code' => 'default_channel',
    ]);

    $channelContext = Mockery::mock(ChannelContext::class);

    $semanticButtonProvider = new SemanticButtonsProvider(
        $semanticButtonFactory,
        $upplerDynamicEntityService, $channelContext);

    $result = $semanticButtonProvider->provide(Mockery::mock(Operation::class));
    \expect($result)->toBeArray();
})->group('IntegrationSemanticButtonProvider');
