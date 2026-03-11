<?php

declare(strict_types=1);

namespace App\Dto;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\OpenApi\Model\Operation;
use App\State\Processor\UserAccountPersistProcessor;
use App\State\Provider\UserAccountProvider;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    operations: [
        new Get(
            openapi: new Operation(
                summary: 'Editer un account',
                description: 'Permet d\'enregistrer des modifications dans un account uppler'
            )
        ),
        new Patch(
            openapi: new Operation(
                summary: 'Modifier un account',
                description: 'Permet d\'enregistrer des modifications dans un account uppler'
            ),
            validate: true,
        ),
        new Post(
            openapi: new Operation(
                summary: 'Déclarer un account Uppler',
                description: 'Permet de créer un account Uppler sur la marketplace\n            en passant toutes les infos de ce compte. Un compte utilisateur est automatiquement créé si nécessaire'
            ),
            validate: true,
        ),
    ],
    provider: UserAccountProvider::class,
    processor: UserAccountPersistProcessor::class
)]
final class UserAccount
{
    #[ApiProperty(identifier: true)]
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private ?Uuid $accountId = null;

    private ?Uuid $userId = null;

    #[Assert\Email(message: 'email required', groups: ['create'])]
    private string $email = '';

    #[Assert\AtLeastOneOf([
        new Assert\IsNull(),
        new Assert\Type('string', message: '(djustCustomerUserId) string required'),
    ], groups: ['create'])]
    private ?string $djustCustomerUserId = null;

    #[Assert\AtLeastOneOf([
        new Assert\IsNull(),
        new Assert\Type('string', message: '(djustCustomerAccountId) string required'),
    ], groups: ['create'])]
    private ?string $djustCustomerAccountId = null;

    #[Assert\AtLeastOneOf([
        new Assert\IsNull(),
        new Assert\Type('string', message: '(djustUsername) string required'),
    ], groups: ['create'])]
    private ?string $djustUsername = null;

    #[Assert\AtLeastOneOf([
        new Assert\IsNull(),
        new Assert\Type('string', message: '(djustPassword) string required'),
    ], groups: ['create'])]
    private ?string $djustPassword = null;

    #[Assert\NotBlank(message: 'contactId cannot be null')]
    #[Assert\Type('string', message: '(contactId) string required')]
    private string $contactId = '';

    #[Assert\NotBlank(message: 'adherentId cannot be null')]
    #[Assert\Type('string', message: '(adherentId) string required')]
    private string $adherentId = '';

    #[Assert\NotBlank(message: 'adherentName cannot be null')]
    #[Assert\Type('string', message: '(adherentName) string required')]
    private string $adherentName = '';

    private ?string $adherentParentId = null;

    #[Assert\NotBlank(message: 'firstname cannot be null')]
    #[Assert\Type('string', message: '(firstname) string required')]
    private string $firstname = '';

    #[Assert\NotBlank(message: 'lastname cannot be null')]
    #[Assert\Type('string', message: '(lastname) string required')]
    private string $lastname = '';

    #[Assert\Type('bool', message: '(isEnabled) bool required')]
    private bool $isEnabled = true;

    private ?string $channelCode = null;

    private ?string $phone = '';

    private ?string $serviceFonction = '';

    #[Assert\AtLeastOneOf([
        new Assert\IsNull(),
        new Assert\Type('integer', message: '(upplerSubAccountId) integer required'),
    ])]
    private ?int $upplerSubAccountId = null;

    #[Assert\AtLeastOneOf([
        new Assert\IsNull(),
        new Assert\Type('integer', message: '(upplerUserId) integer required'),
    ])]
    private ?int $upplerUserId = null;

    #[Assert\AtLeastOneOf([
        new Assert\IsNull(),
        new Assert\Type('integer', message: '(upplerCompanyId) integer required'),
    ])]
    private ?int $upplerCompanyId = null;

    #[Assert\AtLeastOneOf([
        new Assert\IsNull(),
        new Assert\Type('string', message: '(upplerSubAccountClientId) string required'),
    ])]
    private ?string $upplerSubAccountClientId = null;

    #[Assert\AtLeastOneOf([
        new Assert\IsNull(),
        new Assert\Type('string', message: '(upplerSubAccountClientSecret) string required'),
    ])]
    private ?string $upplerSubAccountClientSecret = null;

    public function getAccountId(): ?Uuid
    {
        return $this->accountId;
    }

    public function setAccountId(Uuid $id): ?self
    {
        $this->accountId = $id;

        return $this;
    }

    public function getUserId(): ?Uuid
    {
        return $this->userId;
    }

    public function setUserId(Uuid $id): ?self
    {
        $this->userId = $id;

        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getDjustCustomerAccountId(): ?string
    {
        return $this->djustCustomerAccountId;
    }

    public function setDjustCustomerAccountId(?string $djustCustomerAccountId): self
    {
        $this->djustCustomerAccountId = $djustCustomerAccountId;

        return $this;
    }

    public function getDjustCustomerUserId(): ?string
    {
        return $this->djustCustomerUserId;
    }

    public function setDjustCustomerUserId(?string $djustCustomerUserId): self
    {
        $this->djustCustomerUserId = $djustCustomerUserId;

        return $this;
    }

    public function getDjustUsername(): ?string
    {
        return $this->djustUsername;
    }

    public function setDjustUsername(?string $djustUsername): self
    {
        $this->djustUsername = $djustUsername;

        return $this;
    }

    public function getDjustPassword(): ?string
    {
        return $this->djustPassword;
    }

    public function setDjustPassword(?string $djustPassword): self
    {
        $this->djustPassword = $djustPassword;

        return $this;
    }

    public function getContactId(): string
    {
        return $this->contactId;
    }

    public function setContactId(string $contactId): self
    {
        $this->contactId = $contactId;

        return $this;
    }

    public function getAdherentId(): string
    {
        return $this->adherentId;
    }

    public function setAdherentId(string $adherentId): self
    {
        $this->adherentId = $adherentId;

        return $this;
    }

    public function getAdherentName(): string
    {
        return $this->adherentName;
    }

    public function setAdherentName(string $adherentName): self
    {
        $this->adherentName = $adherentName;

        return $this;
    }

    public function getAdherentParentId(): ?string
    {
        return $this->adherentParentId;
    }

    public function setAdherentParentId(?string $adherentParentId): self
    {
        $this->adherentParentId = $adherentParentId;

        return $this;
    }

    public function getFirstname(): string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function isEnabled(): bool
    {
        return $this->isEnabled;
    }

    public function setIsEnabled(bool $isEnabled): self
    {
        $this->isEnabled = $isEnabled;

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

    public function getServiceFonction(): ?string
    {
        return $this->serviceFonction;
    }

    public function setServiceFonction(?string $serviceFonction): self
    {
        $this->serviceFonction = $serviceFonction;

        return $this;
    }

    public function getChannelCode(): ?string
    {
        return $this->channelCode;
    }

    public function setChannelCode(?string $channelCode): self
    {
        $this->channelCode = $channelCode;

        return $this;
    }

    public function getUpplerSubAccountId(): ?int
    {
        return $this->upplerSubAccountId;
    }

    public function setUpplerSubAccountId(?int $upplerSubAccountId): self
    {
        $this->upplerSubAccountId = $upplerSubAccountId;

        return $this;
    }

    public function getUpplerUserId(): ?int
    {
        return $this->upplerUserId;
    }

    public function setUpplerUserId(?int $upplerUserId): self
    {
        $this->upplerUserId = $upplerUserId;

        return $this;
    }

    public function getUpplerCompanyId(): ?int
    {
        return $this->upplerCompanyId;
    }

    public function setUpplerCompanyId(?int $upplerCompanyId): self
    {
        $this->upplerCompanyId = $upplerCompanyId;

        return $this;
    }

    public function getUpplerSubAccountClientId(): ?string
    {
        return $this->upplerSubAccountClientId;
    }

    public function setUpplerSubAccountClientId(?string $upplerSubAccountClientId): self
    {
        $this->upplerSubAccountClientId = $upplerSubAccountClientId;

        return $this;
    }

    public function getUpplerSubAccountClientSecret(): ?string
    {
        return $this->upplerSubAccountClientSecret;
    }

    public function setUpplerSubAccountClientSecret(?string $upplerSubAccountClientSecret): self
    {
        $this->upplerSubAccountClientSecret = $upplerSubAccountClientSecret;

        return $this;
    }
}
