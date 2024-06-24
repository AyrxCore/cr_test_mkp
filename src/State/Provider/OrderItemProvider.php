<?php

declare(strict_types=1);

namespace App\State\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Dto\OrderItem;

class OrderItemProvider implements ProviderInterface
{
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $orderItem = new OrderItem();
        $orderItem->setId($uriVariables['id']);

        return $orderItem;
    }
}
