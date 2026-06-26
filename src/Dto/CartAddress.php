<?php

declare(strict_types=1);

namespace App\Dto;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\OpenApi\Model\Operation;
use App\State\Processor\CartAddressPersistProcessor;
use App\State\Provider\CartAddressProvider;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    operations: [
        new Get(),
        new Patch(
            uriTemplate: '/cart_addresses/{cartId}',
            requirements: ['id' => '.*'],
            openapi: new Operation(
                summary: 'Update cart addresses'
            ),
            output: false,
        ),
    ],
    provider: CartAddressProvider::class,
    processor: CartAddressPersistProcessor::class
)]
class CartAddress
{
    #[ApiProperty(identifier: true)]
    private ?string $cartId = null;

    #[Assert\Type('string', message: '(billingAddressExternalId) String required')]
    private ?string $billingAddressExternalId = null;

    #[Assert\Type('string', message: '(shippingAddressExternalId) String required')]
    private ?string $shippingAddressExternalId = null;

    public function getCartId(): ?string
    {
        return $this->cartId;
    }

    public function setCartId(?string $cartId): self
    {
        $this->cartId = $cartId;

        return $this;
    }

    public function getShippingAddressExternalId(): ?string
    {
        return $this->shippingAddressExternalId;
    }

    public function setShippingAddressExternalId(string $shippingAddressExternalId): ?self
    {
        $this->shippingAddressExternalId = $shippingAddressExternalId;

        return $this;
    }

    public function getBillingAddressExternalId(): ?string
    {
        return $this->billingAddressExternalId;
    }

    public function setBillingAddressExternalId(string $billingAddressExternalId): ?self
    {
        $this->billingAddressExternalId = $billingAddressExternalId;

        return $this;
    }
}
