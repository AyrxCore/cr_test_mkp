<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Channel;
use App\Repository\ChannelRepository;
use Symfony\Component\HttpFoundation\Request;

trait ChannelAwareControllerTrait
{
    private ?Channel $channel = null;
    private ?ChannelRepository $channelRepository = null;

    public function getChannel(Request $request): ?Channel
    {
        if ($this->channelRepository === null) {
            throw new \RuntimeException('channelRepository is not set. Did you forget to implement ChannelRepositoryAwareControllerInterface?');
        }

        if ($this->channel !== null) {
            return $this->channel;
        }

        $host = $request->headers->get('host');

        $this->channel = $this->channelRepository->findOneBy([
            'hostname' => \preg_replace('/(.*):\d+/', '$1', $host),
        ]);

        return $this->channel;
    }

    public function setChannelRepository(ChannelRepository $channelRepository): void
    {
        $this->channelRepository = $channelRepository;
    }
}
