<?php

declare(strict_types=1);

use App\Context\ChannelContext;
use App\Dto\LegalContent;
use App\Entity\Channel;
use App\Enum\Storyblok\StoryblokEndpoint;
use App\Service\Cgu\LegalContentService;
use App\Service\Cgu\StoryblokToLegalContentMapper;
use App\Service\Storyblok\StoryblokHttpClient;

uses()->group('LegalContentService', 'legal_content');

\beforeEach(function () {
    $this->httpClient = Mockery::mock(StoryblokHttpClient::class);
    $this->mapper = Mockery::mock(StoryblokToLegalContentMapper::class);
    $this->channelContext = Mockery::mock(ChannelContext::class);

    $this->service = new LegalContentService($this->httpClient, $this->mapper, $this->channelContext);
});

\it('returns the CGU for the current channel', function () {
    $channel = Mockery::mock(Channel::class);
    $channel->shouldReceive('getCode')->once()->andReturn('my-channel');
    $this->channelContext->shouldReceive('getChannel')->once()->andReturn($channel);

    $rawData = ['stories' => [['name' => 'my-channel', 'content' => []]]];
    $this->httpClient
        ->shouldReceive('getStories')
        ->once()
        ->with(['starts_with' => StoryblokEndpoint::LEGAL_CONTENT->value])
        ->andReturn($rawData);

    $expectedLegalContent = new LegalContent();
    $this->mapper
        ->shouldReceive('mapByChannelCode')
        ->once()
        ->with($rawData, 'my-channel')
        ->andReturn($expectedLegalContent);

    $result = $this->service->getForCurrentChannel();

    \expect($result)->toBe($expectedLegalContent);
});

\it('returns null when no legal content story matches the channel', function () {
    $channel = Mockery::mock(Channel::class);
    $channel->shouldReceive('getCode')->once()->andReturn('unknown-channel');
    $this->channelContext->shouldReceive('getChannel')->once()->andReturn($channel);

    $rawData = ['stories' => []];
    $this->httpClient
        ->shouldReceive('getStories')
        ->once()
        ->andReturn($rawData);

    $this->mapper
        ->shouldReceive('mapByChannelCode')
        ->once()
        ->andReturn(null);

    $result = $this->service->getForCurrentChannel();

    \expect($result)->toBeNull();
});

