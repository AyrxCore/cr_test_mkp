<?php

declare(strict_types=1);

namespace App\State\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Dto\CartPaymentSepa;

class CartPaymentSepaProvider implements ProviderInterface
{
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): CartPaymentSepa
    {
        $cartPaymentSepa = new CartPaymentSepa();
        $cartPaymentSepa->setId($uriVariables['id']);

        return $cartPaymentSepa;
    }
}
