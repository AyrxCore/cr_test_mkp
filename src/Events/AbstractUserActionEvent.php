<?php

declare(strict_types=1);

namespace App\Events;

use App\Entity\Channel;
use App\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;

abstract class AbstractUserActionEvent extends Event implements UserActionEventInterface
{
    private User $user;
    private Channel $channel;

    public function __construct(User $user, Channel $channel)
    {
        $this->user = $user;
        $this->channel = $channel;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getChannel(): Channel
    {
        return $this->channel;
    }
}
