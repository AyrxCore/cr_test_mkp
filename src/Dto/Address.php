<?php

declare(strict_types=1);

namespace App\Dto;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\OpenApi\Model\Operation;
use App\State\Processor\AddressPersistProcessor;
use App\State\Provider\AddressProvider;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    operations: [
        new Get(),
        new Put(
            openapi: new Operation(
                summary: 'Editer une adresse',
                description: 'Permet d\'éditer adresse'
            ),
            normalizationContext: ['groups' => ['address:update']],
            validate: true
        ),
        new Post(
            openapi: new Operation(
                summary: 'Créer une nouvelle adresse',
                description: 'Permet de créer une nouvelle adresse'
            ),
            normalizationContext: ['groups' => ['address:create']],
            validate: true
        ),
        new GetCollection(),
    ],
    provider: AddressProvider::class,
    processor: AddressPersistProcessor::class
)]
final class Address
{
    #[ApiProperty(identifier: true)]
    #[Groups(['address:update'])]
    private ?string $id = null;

    #[Groups(['address:update'])]
    private ?string $externalId = null;

    #[Assert\Type('string', message: '(name) string required')]
    #[Groups(['address:create', 'address:update'])]
    private ?string $fullName;

    #[Groups(['address:create', 'address:update'])]
    private bool $shipping = false;

    #[Groups(['address:create', 'address:update'])]
    private bool $billing = false;

    #[Assert\NotBlank(message: 'street cannot be null', groups: ['create'])]
    #[Groups(['address:create', 'address:update'])]
    private string $address;

    #[Assert\NotBlank(message: 'postcode cannot be null')]
    #[Groups(['address:create', 'address:update'])]
    private string $zipcode;

    #[Assert\NotBlank(message: 'city cannot be null')]
    #[Assert\Type('string', message: '(city) string required')]
    #[Groups(['address:create', 'address:update'])]
    private string $city;

    #[Assert\NotBlank(message: 'country cannot be null')]
    #[Assert\Type('string', message: '(country) int required')]
    #[Groups(['address:create', 'address:update'])]
    private string $country;

    #[Groups(['address:create', 'address:update'])]
    private ?string $phone;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(?string $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getExternalId(): ?string
    {
        return $this->externalId;
    }

    public function setExternalId(?string $externalId): self
    {
        $this->externalId = $externalId;

        return $this;
    }

    public function getFullName(): string
    {
        return $this->fullName;
    }

    public function setFullName(string $fullName): self
    {
        $this->fullName = $fullName;

        return $this;
    }

    public function isShipping(): bool
    {
        return $this->shipping;
    }

    public function setShipping(bool $shipping): self
    {
        $this->shipping = $shipping;

        return $this;
    }

    public function isBilling(): bool
    {
        return $this->billing;
    }

    public function setBilling(bool $billing): self
    {
        $this->billing = $billing;

        return $this;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getZipCode(): string
    {
        return $this->zipcode;
    }

    public function setZipcode(string $zipcode): self
    {
        $this->zipcode = $zipcode;

        return $this;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getCountry(): string
    {
        return $this->country;
    }

    public function setCountry(string $country): self
    {
        $this->country = $country;

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
}
