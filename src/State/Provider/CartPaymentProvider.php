<?php

declare(strict_types=1);

namespace App\State\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Dto\CartPayment;

class CartPaymentProvider implements ProviderInterface
{
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $cartPayment = new CartPayment();
        $cartPayment->setId($uriVariables['id']);

        return $cartPayment;
    }
}
