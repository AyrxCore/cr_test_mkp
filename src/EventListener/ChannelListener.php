<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Entity\Channel;
use App\Service\Channel\ChannelOptionSynchronizer;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class ChannelListener
{
    public function __construct(private ChannelOptionSynchronizer $channelOptionSynchronizer)
    {
    }

    public function postLoad(LifecycleEventArgs $event): void
    {
        /** @var Channel $channel */
        $channel = $event->getObject();

        if ($channel instanceof Channel) {
            $this->channelOptionSynchronizer->syncChannelOptions($channel);
        }
    }
}
