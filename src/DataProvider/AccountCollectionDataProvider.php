<?php

declare(strict_types=1);

namespace App\DataProvider;

use App\Entity\Account;

class AccountCollectionDataProvider extends AbstractCollectionDataProvider
{
    /**
     * @param Account $object
     */
    protected function skip($object): bool
    {
        return !$object->isEnabled();
    }

    protected function getRessourceClass(): string
    {
        return Account::class;
    }
}
