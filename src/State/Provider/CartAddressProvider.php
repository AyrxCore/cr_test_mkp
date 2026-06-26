<?php

declare(strict_types=1);

namespace App\State\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Dto\CartAddress;

readonly class CartAddressProvider implements ProviderInterface
{
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $cartAddress = new CartAddress();
        $cartAddress->setCartId($uriVariables['cartId'] ?? null);

        return $cartAddress;
    }
}
