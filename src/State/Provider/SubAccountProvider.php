<?php

declare(strict_types=1);

namespace App\State\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Dto\SubAccount;

readonly class SubAccountProvider implements ProviderInterface
{
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): SubAccount
    {
        $subAccount = new SubAccount();

        if (isset($uriVariables['id'])) {
            $subAccount->setId((string) $uriVariables['id']);
        }

        return $subAccount;
    }
}
