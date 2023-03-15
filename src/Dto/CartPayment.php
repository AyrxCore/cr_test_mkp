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
                'summary' => 'Update cart payment method',
            ],
            'method' => 'PATCH',
            'validate' => true,
        ],
    ]
)]
final class CartPayment
{
    #[ApiProperty(identifier: true)]
    private ?int $id = null;

    #[Assert\Type('integer', message: '(paymentMethodId) Integer required')]
    private ?int $paymentMethodId = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getPaymentMethodId(): ?int
    {
        return $this->paymentMethodId;
    }

    public function setPaymentMethodId(int $paymentMethodId): ?self
    {
        $this->paymentMethodId = $paymentMethodId;

        return $this;
    }
}
