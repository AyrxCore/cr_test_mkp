<?php

declare(strict_types=1);

namespace App\Events;

use App\Entity\Account;

class UserAcceptStellantisModalEvent
{
    private Account $account;

    public function __construct(Account $account)
    {
        $this->account = $account;
    }

    public function getAccount(): Account
    {
        return $this->account;
    }
}
