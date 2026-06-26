<?php

declare(strict_types=1);

namespace App\Service\Djust;

use App\Context\ChannelContext;

class DjustStoreViewHeadersBuilder
{
    private const string STORE_VIEW_HEADER = 'dj-store-view';

    public function __construct(private ChannelContext $channelContext) {}

    public function build(): array
    {
        return [self::STORE_VIEW_HEADER => $this->channelContext->getChannel()->getCode()];
    }
}
