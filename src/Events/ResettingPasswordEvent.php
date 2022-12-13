<?php

namespace App\Events;



use App\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;

class ResettingPasswordEvent extends Event
{
    protected $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function setUser(User $user)
    {
        $this->user = $user;
        return $this;
    }


}
