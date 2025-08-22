<?php

declare(strict_types=1);

namespace App\Dto;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\State\Processor\AddressPersistProcessor;
use App\State\Provider\AddressProvider;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    operations: [
        new Get(),
        new Put(
            openapiContext: ['summary' => 'Editer une adresse', 'description' => 'Permet d\'éditer adresse pour une company'],
            normalizationContext: ['groups' => ['address:update']],
            validate: true
        ),
        new Post(
            openapiContext: ['summary' => 'Créer une nouvelle adresse', 'description' => 'Permet de créer une nouvelle adresse pour une company'],
            normalizationContext: ['groups' => ['address:create']],
            validate: true
        ),
        new GetCollection()
    ],
    provider: AddressProvider::class,
    processor: AddressPersistProcessor::class
)]
final class Address
{
    #[ApiProperty(identifier: true)]
    #[Groups(['address:update'])]
    private ?int $id = null;

    #[Assert\Type('integer', message: '(upplerSubAccountId) Integer required', groups: ['create'])]
    #[Groups(['address:update'])]
    private int $companyId;

    #[Assert\Type('string', message: '(name) string required')]
    #[Groups(['address:create', 'address:update'])]
    private ?string $name;

    #[Groups(['address:create', 'address:update'])]
    private string|null $type;

    #[Assert\NotBlank(message: 'company cannot be null', groups: ['create'])]
    #[Groups(['address:create', 'address:update'])]
    private string $company;

    #[Assert\NotBlank(message: 'street cannot be null', groups: ['create'])]
    #[Groups(['address:create', 'address:update'])]
    private string $street;

    #[Assert\NotBlank(message: 'postcode cannot be null')]
    #[Groups(['address:create', 'address:update'])]
    private string $postcode;

    #[Assert\NotBlank(message: 'city cannot be null')]
    #[Assert\Type('string', message: '(city) string required')]
    #[Groups(['address:create', 'address:update'])]
    private string $city;

    #[Assert\NotBlank(message: 'country cannot be null')]
    #[Assert\Type('integer', message: '(country) int required')]
    #[Groups(['address:create', 'address:update'])]
    private int $country;

    #[Assert\Type('string', message: '(lastName) string required')]
    #[Groups(['address:create', 'address:update'])]
    private ?string $lastName;

    #[Assert\Type('string', message: '(firstName) string required')]
    #[Groups(['address:create', 'address:update'])]
    private ?string $firstName;

    #[Assert\Type('string', message: '(phone) string required')]
    #[Groups(['address:create', 'address:update'])]
    private ?string $phone;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getCompanyId(): ?int
    {
        return $this->companyId;
    }

    public function setCompanyId(?int $companyId): self
    {
        $this->companyId = $companyId;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getType(): string|null
    {
        return $this->type;
    }

    public function setType(string|null $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getCompany(): string
    {
        return $this->company;
    }

    public function setCompany(string $company): self
    {
        $this->company = $company;

        return $this;
    }

    public function getStreet(): string
    {
        return $this->street;
    }

    public function setStreet(string $street): self
    {
        $this->street = $street;

        return $this;
    }

    public function getPostCode(): string
    {
        return $this->postcode;
    }

    public function setPostCode(string $postCode): self
    {
        $this->postcode = $postCode;

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

    public function getCountry(): int
    {
        return $this->country;
    }

    public function setCountry(int $country): self
    {
        $this->country = $country;

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
}
