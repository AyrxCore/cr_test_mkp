<?php

declare(strict_types=1);

namespace App\Dto;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    collectionOperations: ['POST'],
    itemOperations: [
        'get',
        'update' => [
            'openapi_context' => [
                'summary' => 'Update order shipping',
            ],
            'method' => 'PATCH',
            'validate' => true,
        ],
    ]
)]
final class OrderShipping
{
    #[ApiProperty(identifier: true)]
    private ?int $id = null;

    #[Assert\Type('integer', message: '(cartId) Integer required')]
    private ?int $cartId = null;
    #[Assert\Type('integer', message: '(shippingId) Integer required')]
    private ?int $shippingId = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getCartId(): ?int
    {
        return $this->cartId;
    }

    public function setCartId(int $cartId): ?self
    {
        $this->cartId = $cartId;

        return $this;
    }

    public function getShippingId(): ?int
    {
        return $this->shippingId;
    }

    public function setShippingId(int $shippingId): ?self
    {
        $this->shippingId = $shippingId;

        return $this;
    }
}
