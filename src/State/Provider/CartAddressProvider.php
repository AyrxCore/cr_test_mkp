<?php

declare(strict_types=1);

namespace App\State\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Dto\CartAddress;

class CartAddressProvider implements ProviderInterface
{
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $cartAdress = new CartAddress();
        $cartAdress->setId($uriVariables['id']);

        return $cartAdress;
    }
}
