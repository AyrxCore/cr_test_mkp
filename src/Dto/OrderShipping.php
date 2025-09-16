<?php

declare(strict_types=1);

namespace App\Dto;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\OpenApi\Model\Operation;
use App\State\Processor\OrderShippingPersistProcessor;
use App\State\Provider\OrderShippingProvider;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    operations: [
        new Get(),
        new Patch(
            openapi: new Operation(
                summary: 'Update order shipping'
            ),
            validate: true
        ),
        new Post(),
    ],
    provider: OrderShippingProvider::class,
    processor: OrderShippingPersistProcessor::class
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
