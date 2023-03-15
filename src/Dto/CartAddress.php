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
                'summary' => 'Update cart adresses',
            ],
            'method' => 'PATCH',
            'validate' => true,
        ],
    ]
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
