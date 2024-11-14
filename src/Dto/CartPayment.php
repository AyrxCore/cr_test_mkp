<?php

declare(strict_types=1);

namespace App\Dto;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\State\Processor\CartPaymentPersistProcessor;
use App\State\Provider\CartPaymentProvider;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    operations: [
        new Get(),
        new Patch(
            openapiContext: ['summary' => 'Update cart payment method'],
            validate: true
        ),
        new Post(),
    ],
    provider: CartPaymentProvider::class,
    processor: CartPaymentPersistProcessor::class
)]
final class CartPayment
{
    // IDs des méthodes de paiements Uppler
    public const CART_PAYMENT_CB = 8;
    public const CART_PAYMENT_SEPA = [9, 10];
    public const CART_PAYMENT_MANDAT_ADMIN = 11;

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
