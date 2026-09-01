<?php

declare(strict_types=1);

use App\Context\ChannelContext;
use App\Dto\SemanticButton\SemanticButton;
use App\Entity\Channel;
use App\Enum\Storyblok\StoryblokEndpoint;
use App\Service\SemanticButton\SemanticButtonService;
use App\Service\SemanticButton\StoryblokToSemanticButtonMapper;
use App\Service\Storyblok\StoryblokHttpClient;

uses()->group('SemanticButtonService', 'semantic_button');

\beforeEach(function () {
    $this->httpClient = Mockery::mock(StoryblokHttpClient::class);
    $this->mapper = Mockery::mock(StoryblokToSemanticButtonMapper::class);
    $this->channelContext = Mockery::mock(ChannelContext::class);

    $this->service = new SemanticButtonService($this->httpClient, $this->mapper, $this->channelContext);
});

\it('returns the semantic buttons for the current channel', function () {
    $channel = Mockery::mock(Channel::class);
    $channel->shouldReceive('getCode')->once()->andReturn(Channel::QANTIS_ACHAT);
    $this->channelContext->shouldReceive('getChannel')->once()->andReturn($channel);

    $rawData = ['stories' => [['name' => Channel::QANTIS_ACHAT, 'content' => []]]];
    $this->httpClient
        ->shouldReceive('getStories')
        ->once()
        ->with(['starts_with' => StoryblokEndpoint::MARQUES_BLANCHES->value])
        ->andReturn($rawData);

    $expectedSemanticButtons = [new SemanticButton()];
    $this->mapper
        ->shouldReceive('mapByChannelCode')
        ->once()
        ->with($rawData, Channel::QANTIS_ACHAT)
        ->andReturn($expectedSemanticButtons);

    $result = $this->service->getForCurrentChannel();

    \expect($result)->toBe($expectedSemanticButtons);
});

\it('returns an empty array when no semantic buttons match the current channel', function () {
    $channel = Mockery::mock(Channel::class);
    $channel->shouldReceive('getCode')->once()->andReturn('UNKNOWN_CHANNEL');
    $this->channelContext->shouldReceive('getChannel')->once()->andReturn($channel);

    $rawData = ['stories' => []];
    $this->httpClient
        ->shouldReceive('getStories')
        ->once()
        ->with(['starts_with' => StoryblokEndpoint::MARQUES_BLANCHES->value])
        ->andReturn($rawData);

    $this->mapper
        ->shouldReceive('mapByChannelCode')
        ->once()
        ->with($rawData, 'UNKNOWN_CHANNEL')
        ->andReturn([]);

    $result = $this->service->getForCurrentChannel();

    \expect($result)->toBeArray()->toBeEmpty();
});
