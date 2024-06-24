<?php

declare(strict_types=1);

namespace App\Dto;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\State\Processor\CartAddressPersistProcessor;
use App\State\Provider\CartAddressProvider;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    operations: [
        new Get(),
        new Patch(
            openapiContext: ['summary' => 'Update cart adresses'],
            validate: true
        ),
        new Post()
    ],
    provider: CartAddressProvider::class,
    processor: CartAddressPersistProcessor::class
)]
final class CartAddress
{
    #[ApiProperty(identifier: true)]
    private ?int $id = null;

    #[Assert\Type('integer', message: '(billingAddressId) Integer required')]
    private ?int $billingAddressId = null;
    #[Assert\Type('integer', message: '(shippingAddressId) Integer required')]
    private ?int $shippingAddressId = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getShippingAddressId(): ?int
    {
        return $this->shippingAddressId;
    }

    public function setShippingAddressId(int $shippingAddressId): ?self
    {
        $this->shippingAddressId = $shippingAddressId;

        return $this;
    }

    public function getBillingAddressId(): ?int
    {
        return $this->billingAddressId;
    }

    public function setBillingAddressId(int $billingAddressId): ?self
    {
        $this->billingAddressId = $billingAddressId;

        return $this;
    }
}
