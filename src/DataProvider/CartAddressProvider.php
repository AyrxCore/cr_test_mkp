<?php

declare(strict_types=1);

namespace App\DataProvider;

use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Dto\CartAddress;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Service\Attribute\Required;


class CartAddressProvider implements RestrictedDataProviderInterface, ItemDataProviderInterface
{
    #[Required]
    public EntityManagerInterface $em;

    public function getItem(string $resourceClass, $id, string $operationName = null, array $context = [])
    {
        $cartAdress = new CartAddress();
        $cartAdress->setId($id);
        return $cartAdress;
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return CartAddress::class === $resourceClass;
    }
}
