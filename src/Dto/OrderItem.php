<?php

declare(strict_types=1);

namespace App\Dto;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\Api\addOrderItemToCart;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    collectionOperations: [
        'post_item_to_cart' => [
            'openapi_context' => [
                'summary' => 'Add item to cart',
            ],
            'method' => 'POST',
            'path' => '/order_items',
            'controller' => addOrderItemToCart::class,
        ],
    ],
    itemOperations: [
        'get',
        'update' => [
            'openapi_context' => [
                'summary' => 'Update order item quantity',
            ],
            'method' => 'PATCH',
            'validate' => true,
        ],
        'delete' => [
            'openapi_context' => [
                'summary' => 'Delete order item',
            ],
            'method' => 'DELETE',
            'validate' => true,
        ],
    ]
)]
final class OrderItem
{
    #[ApiProperty(identifier: true)]
    private ?int $id = null;

    #[Assert\NotBlank]
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
