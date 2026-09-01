<?php

declare(strict_types=1);

namespace App\Service\SemanticButton;

use App\Context\ChannelContext;
use App\Enum\Storyblok\StoryblokEndpoint;
use App\Service\Storyblok\StoryblokHttpClient;

final class SemanticButtonService
{
    public function __construct(
        private readonly StoryblokHttpClient $httpClient,
        private readonly StoryblokToSemanticButtonMapper $mapper,
        private readonly ChannelContext $channelContext,
    ) {
    }

    /**
     * @return \App\Dto\SemanticButton\SemanticButton[]
     */
    public function getForCurrentChannel(): array
    {
        $channelCode = $this->channelContext->getChannel()->getCode();

        $rawData = $this->httpClient->getStories([
            'starts_with' => StoryblokEndpoint::MARQUES_BLANCHES->value,
        ]);

        return $this->mapper->mapByChannelCode($rawData, $channelCode);
    }
}
