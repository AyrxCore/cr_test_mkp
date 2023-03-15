<?php

declare(strict_types=1);

namespace App\DataProvider;

use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Dto\OrderShipping;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Service\Attribute\Required;


class OrderShippingProvider implements RestrictedDataProviderInterface, ItemDataProviderInterface
{
    #[Required]
    public EntityManagerInterface $em;

    public function getItem(string $resourceClass, $id, string $operationName = null, array $context = [])
    {
        $orderShipping = new OrderShipping();
        $orderShipping->setId($id);
        return $orderShipping;
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return OrderShipping::class === $resourceClass;
    }
}
