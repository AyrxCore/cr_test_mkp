<?php

declare(strict_types=1);

namespace App\Events;

use App\Entity\Channel;
use App\Entity\User;

interface UserActionEventInterface
{
    public function getUser(): User;

    public function getChannel(): Channel;
}
