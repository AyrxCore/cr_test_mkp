<?php

declare(strict_types=1);

namespace App\State\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Dto\OrderShipping;

class OrderShippingProvider implements ProviderInterface
{
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $orderShipping = new OrderShipping();
        $orderShipping->setId($uriVariables['id']);

        return $orderShipping;
    }
}
