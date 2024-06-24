<?php

declare(strict_types=1);

namespace App\State\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Repository\AccountRepository;

readonly class UserAccountProvider implements ProviderInterface
{
    public function __construct(private AccountRepository $accountRepository)
    {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        return $this->accountRepository->find($uriVariables['accountId']);
    }
}
