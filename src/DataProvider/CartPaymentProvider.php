<?php

declare(strict_types=1);

namespace App\DataProvider;

use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Dto\CartPayment;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Service\Attribute\Required;


class CartPaymentProvider implements RestrictedDataProviderInterface, ItemDataProviderInterface
{
    #[Required]
    public EntityManagerInterface $em;

    public function getItem(string $resourceClass, $id, string $operationName = null, array $context = [])
    {
        $cartPayment = new CartPayment();
        $cartPayment->setId($id);
        return $cartPayment;
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return CartPayment::class === $resourceClass;
    }
}
