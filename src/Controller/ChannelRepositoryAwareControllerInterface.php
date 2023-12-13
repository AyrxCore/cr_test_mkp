<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\ChannelRepository;

interface ChannelRepositoryAwareControllerInterface
{
    public function setChannelRepository(ChannelRepository $channelRepository);
}
