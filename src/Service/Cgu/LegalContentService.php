<?php

declare(strict_types=1);

namespace App\Service\Cgu;

use App\Context\ChannelContext;
use App\Dto\LegalContent;
use App\Entity\Channel;
use App\Enum\Storyblok\StoryblokEndpoint;
use App\Service\Storyblok\StoryblokHttpClient;

final class LegalContentService
{
    private const array CHANNEL_CODES_WITH_DEDICATED_CGU = ['OPTEAM', 'CAPER'];

    public function __construct(
        private readonly StoryblokHttpClient $httpClient,
        private readonly StoryblokToLegalContentMapper $mapper,
        private readonly ChannelContext $channelContext,
    ) {
    }

    public function getForCurrentChannel(): ?LegalContent
    {
        $channelCode = $this->channelContext->getChannel()->getCode();

        if (!\in_array($channelCode, self::CHANNEL_CODES_WITH_DEDICATED_CGU, true)) {
            $channelCode = Channel::QANTIS_ACHAT;
        }

        $rawData = $this->httpClient->getStories([
            'starts_with' => StoryblokEndpoint::LEGAL_CONTENT->value,
        ]);

        return $this->mapper->mapByChannelCode($rawData, $channelCode);
    }
}
