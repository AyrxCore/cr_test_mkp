<?php

declare(strict_types=1);

namespace App\Dto;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\OpenApi\Model\Operation;
use App\Controller\Api\AddOrderItemToCart;
use App\State\Processor\OrderItemPersistProcessor;
use App\State\Processor\OrderItemRemoveProcessor;
use App\State\Provider\OrderItemProvider;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    operations: [
        new Get(),
        new Patch(
            openapi: new Operation(
                summary: 'Update order item quantity'
            ),
            validate: true,
            validationContext: ['groups' => ['update']],
            processor: OrderItemPersistProcessor::class
        ),
        new Delete(
            openapi: new Operation(
                summary: 'Delete order item'
            ),
            validate: true,
            processor: OrderItemRemoveProcessor::class
        ),
        new Post(
            uriTemplate: '/order_items',
            controller: AddOrderItemToCart::class,
            openapi: new Operation(
                summary: 'Add item to cart'
            )
        ),
    ],
    provider: OrderItemProvider::class
)]
final class OrderItem
{
    #[ApiProperty(identifier: true)]
    private ?int $id = null;

    #[Assert\NotBlank(groups: ['create', 'update'])]
    #[Assert\Type('integer', message: '(quantity) Integer required')]
    private ?int $quantity = null;
    #[Assert\Type('integer', message: '(cartId) Integer required')]
    private ?int $cartId = null;
    #[Assert\Type('integer', message: '(variantId) Integer required')]
    private ?int $variantId = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): ?self
    {
        $this->quantity = $quantity;

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

    public function getVariantId(): ?int
    {
        return $this->variantId;
    }

    public function setVariantId(int $variantId): ?self
    {
        $this->variantId = $variantId;

        return $this;
    }
}
