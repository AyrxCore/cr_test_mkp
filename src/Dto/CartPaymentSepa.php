<?php

declare(strict_types=1);

namespace App\Dto;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    itemOperations: [
        'update' => [
            'openapi_context' => [
                'summary' => 'Update cart sepa informations',
            ],
            'method' => 'PATCH',
            'validate' => true,
        ],
    ]
)]
final class CartPaymentSepa
{
    #[ApiProperty(identifier: true)]
    private int $id;

    #[Assert\Type('string', message: '(iban) String')]
    private ?string $iban = null;

    #[Assert\Type('string', message: '(bic) String')]
    private ?string $bic = null;

    #[Assert\Type('string', message: '(ownerName) String')]
    private ?string $ownerName = null;

    #[Assert\Type('string', message: '(phone) String')]
    private ?string $phone = null;

    #[Assert\Type('integer', message: '(mandateId) Integer')]
    private ?int $mandateId = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getIban(): ?string
    {
        return $this->iban;
    }

    public function setIban(string $iban): ?self
    {
        $this->iban = $iban;

        return $this;
    }

    public function getBic(): ?string
    {
        return $this->bic;
    }

    public function setBic(string $bic): ?self
    {
        $this->bic = $bic;

        return $this;
    }

    public function getOwnerName(): ?string
    {
        return $this->ownerName;
    }

    public function setOwnerName(string $ownerName): ?self
    {
        $this->ownerName = $ownerName;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): ?self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getMandateId(): ?int
    {
        return $this->mandateId;
    }

    public function setMandateId(int $mandateId): ?self
    {
        $this->mandateId = $mandateId;

        return $this;
    }
}
