<?php

declare(strict_types=1);

namespace App\DataProvider;

use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Dto\OrderItem;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Service\Attribute\Required;


class OrderItemProvider implements RestrictedDataProviderInterface, ItemDataProviderInterface
{
    #[Required]
    public EntityManagerInterface $em;

    public function getItem(string $resourceClass, $id, string $operationName = null, array $context = [])
    {
        $orderItem = new OrderItem();
        $orderItem->setId($id);
        return $orderItem;
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return OrderItem::class === $resourceClass;
    }
}
