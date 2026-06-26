<?php

declare(strict_types=1);

namespace App\Dto;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\OpenApi\Model\Operation;
use App\State\Processor\CartPaymentPersistProcessor;
use App\State\Provider\CartPaymentProvider;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    operations: [
        new Get(),
        new Patch(
            openapi: new Operation(
                summary: 'Update cart payment method'
            ),
            validate: true
        ),
        new Post(),
    ],
    provider: CartPaymentProvider::class,
    processor: CartPaymentPersistProcessor::class
)]
final class CartPayment
{
    // Types de paiement Adyen
    public const PAYMENT_TYPE_CB = 'scheme';
    public const PAYMENT_TYPE_SEPA = 'bankTransfer_IBAN';

    #[ApiProperty(identifier: true)]
    private ?int $id = null;

    #[Assert\NotNull(message: '(paymentMethodType) Required')]
    #[Assert\Choice(
        choices: [self::PAYMENT_TYPE_CB, self::PAYMENT_TYPE_SEPA],
        message: '(paymentMethodType) Invalid payment method type'
    )]
    private ?string $paymentMethodType = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getPaymentMethodType(): ?string
    {
        return $this->paymentMethodType;
    }

    public function setPaymentMethodType(string $paymentMethodType): self
    {
        $this->paymentMethodType = $paymentMethodType;

        return $this;
    }
}
