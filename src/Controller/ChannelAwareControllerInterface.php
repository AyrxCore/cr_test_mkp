<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Channel;
use Symfony\Component\HttpFoundation\Request;

interface ChannelAwareControllerInterface extends ChannelRepositoryAwareControllerInterface
{
    public function getChannel(Request $request): ?Channel;
}
