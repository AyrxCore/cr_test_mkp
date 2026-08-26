<?php

declare(strict_types=1);

namespace App\Context;

use App\Entity\Channel;
use App\Exception\MissingChannelHeaderException;
use App\Repository\ChannelRepository;
use Symfony\Component\HttpFoundation\RequestStack;

class ChannelContext
{
    public function __construct(
        private RequestStack $requestStack,
        private ChannelRepository $channelRepository,
    ) {
    }

    public function getChannel(): Channel
    {
        if (!$request = $this->requestStack->getMainRequest()) {
            throw new \RuntimeException('Unable to resolve channel because there is no request.');
        }

        if (!$code = $request->headers->get('X-Channel')) {
            throw new MissingChannelHeaderException('You must set the "X-Channel" header to a registered channel.');
        }

        if (!$channel = $this->channelRepository->findOneByCode($code)) {
            throw new MissingChannelHeaderException(\sprintf('No such channel "%s".', $code));
        }

        return $channel;
    }
}
