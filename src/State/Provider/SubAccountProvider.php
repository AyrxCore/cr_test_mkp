<?php

declare(strict_types=1);

namespace App\State\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Service\UpplerAccountService;

readonly class SubAccountProvider implements ProviderInterface
{
    public function __construct(private UpplerAccountService $upplerAccountService)
    {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        return $this->upplerAccountService->getUserSubAccountData();
    }
}
