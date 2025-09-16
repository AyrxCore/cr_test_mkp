<?php

declare(strict_types=1);

namespace App\Dto;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\OpenApi\Model\Operation;
use App\State\Processor\SubAccountPersistProcessor;
use App\State\Provider\SubAccountProvider;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    operations: [
        new Get(),
        new Patch(
            openapi: new Operation(
                summary: 'Editer un account',
                description: 'Permet d\'enregistrer des modifications dans un account uppler'
            ),
            normalizationContext: ['groups' => ['update']],
            validate: true
        ),
    ],
    provider: SubAccountProvider::class,
    processor: SubAccountPersistProcessor::class
)]
final class SubAccount
{
    #[ApiProperty(identifier: true)]
    private ?int $id = null;

    #[Assert\Type('string', message: '(email) string required')]
    private ?string $email = null;

    #[Assert\Type('string', message: '(lastname) string required')]
    private ?string $lastName = null;

    #[Assert\Type('string', message: '(firstname) string required')]
    private ?string $firstName = null;

    private ?string $phone = '';

    #[Assert\Type('integer', message: '(shipping_address_id) Integer required')]
    private ?int $shippingAddressId = null;

    #[Assert\Type('integer', message: '(billing_address_id) Integer required')]
    private ?int $billingAddressId = null;

    private ?Uuid $accountId = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getShippingAddressId(): ?int
    {
        return $this->shippingAddressId;
    }

    public function setShippingAddressId(?int $shippingAddressId): ?self
    {
        $this->shippingAddressId = $shippingAddressId;

        return $this;
    }

    public function getBillingAddressId(): ?int
    {
        return $this->billingAddressId;
    }

    public function setBillingAddressId(?int $billingAddressId): ?self
    {
        $this->billingAddressId = $billingAddressId;

        return $this;
    }

    public function getAccountId(): ?Uuid
    {
        return $this->accountId;
    }

    public function setAccountId(?Uuid $accountId): void
    {
        $this->accountId = $accountId;
    }
}
