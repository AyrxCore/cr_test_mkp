<?php

namespace App\Events;



use App\Entity\Account;
use Symfony\Contracts\EventDispatcher\Event;

class UserAcceptCGUEvent extends Event
{
    protected Account $account;

    public function __construct(Account $account)
    {
        $this->account = $account;
    }

    /**
     * @return Account
     */
    public function getAccount(): Account
    {
        return $this->account;
    }

    /**
     * @param Account $account
     */
    public function setAccount(Account $account): void
    {
        $this->account = $account;
    }
}
