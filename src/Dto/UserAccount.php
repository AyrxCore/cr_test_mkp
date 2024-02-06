<?php

declare(strict_types=1);

namespace App\Dto;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    collectionOperations: [
        'create' => [
            'openapi_context' => [
                'summary' => 'Déclarer un account Uppler',
                'description' => 'Permet de créer un account Uppler sur la marketplace
            en passant toutes les infos de ce compte. Un compte utilisateur est automatiquement créé si nécessaire',
            ],
            'method' => 'POST',
            'validate' => true,
        ],
    ],
    itemOperations: [
        'get' => [
            'openapi_context' => [
                'summary' => 'Editer un account',
                'description' => "Permet d'enregistrer des modifications dans un account uppler",
            ],
        ],
        'update' => [
            'openapi_context' => [
                'summary' => 'Modifier un account',
                'description' => "Permet d'enregistrer des modifications dans un account uppler",
            ],
            'method' => 'PATCH',
            'validate' => true,
        ],
    ]
)]
final class UserAccount
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    #[ApiProperty(identifier: true)]
    private ?Uuid $accountId = null;

    private ?Uuid $userId = null;

    #[Assert\Email(message: 'email required', groups: ['create'])]
    private string $email = '';

    #[Assert\NotBlank(message: 'upplerSubAccountId cannot be null', groups: ['create'])]
    #[Assert\Type('integer', message: '(upplerSubAccountId) Integer required', groups: ['create'])]
    private ?int $upplerSubAccountId = null;

    #[Assert\NotBlank(message: 'upplerUserId cannot be null', groups: ['create'])]
    #[Assert\Type('integer', message: '(upplerUserId) integer required', groups: ['create'])]
    private ?int $upplerUserId = null;

    #[Assert\NotBlank(message: 'upplerCompanyId cannot be null')]
    #[Assert\Type('integer', message: '(upplerCompanyId) integer required')]
    private ?int $upplerCompanyId = null;

    #[Assert\NotBlank(message: 'upplerSubAccountClientId cannot be null')]
    #[Assert\Type('string', message: '(upplerSubAccountClientId) string required')]
    private string $upplerSubAccountClientId = '';

    #[Assert\NotBlank(message: 'upplerSubAccountClientSecret cannot be null')]
    #[Assert\Type('string', message: '(upplerSubAccountClientSecret) string required')]
    private string $upplerSubAccountClientSecret = '';

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

    public function getAccountId(): ?Uuid
    {
        return $this->accountId;
    }

    public function setUserId(Uuid $id): ?self
    {
        $this->userId = $id;

        return $this;
    }

    public function getUserId(): ?Uuid
    {
        return $this->userId;
    }

    public function setAccountId(Uuid $id): ?self
    {
        $this->accountId = $id;

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

    public function getUpplerSubAccountId(): ?int
    {
        return $this->upplerSubAccountId;
    }

    public function setUpplerSubAccountId(int $upplerSubAccountId): self
    {
        $this->upplerSubAccountId = $upplerSubAccountId;

        return $this;
    }

    public function getUpplerUserId(): ?int
    {
        return $this->upplerUserId;
    }

    public function setUpplerUserId(int $upplerUserId): self
    {
        $this->upplerUserId = $upplerUserId;

        return $this;
    }

    public function getUpplerCompanyId(): ?int
    {
        return $this->upplerCompanyId;
    }

    public function setUpplerCompanyId(int $upplerCompanyId): self
    {
        $this->upplerCompanyId = $upplerCompanyId;

        return $this;
    }

    public function getUpplerSubAccountClientId(): ?string
    {
        return $this->upplerSubAccountClientId;
    }

    public function setUpplerSubAccountClientId(string $upplerSubAccountClientId): self
    {
        $this->upplerSubAccountClientId = $upplerSubAccountClientId;

        return $this;
    }

    public function getUpplerSubAccountClientSecret(): ?string
    {
        return $this->upplerSubAccountClientSecret;
    }

    public function setUpplerSubAccountClientSecret(string $upplerSubAccountClientSecret): self
    {
        $this->upplerSubAccountClientSecret = $upplerSubAccountClientSecret;

        return $this;
    }

    public function getAdherentId(): string
    {
        return $this->adherentId;
    }

    public function setAdherentId(string $adherentId): void
    {
        $this->adherentId = $adherentId;
    }

    public function getAdherentName(): string
    {
        return $this->adherentName;
    }

    public function setAdherentName(string $adherentName): void
    {
        $this->adherentName = $adherentName;
    }

    public function getAdherentParentId(): ?string
    {
        return $this->adherentParentId;
    }

    public function setAdherentParentId(?string $adherentParentId): void
    {
        $this->adherentParentId = $adherentParentId;
    }

    public function getFirstname(): string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): void
    {
        $this->firstname = $firstname;
    }

    public function getLastname(): string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): void
    {
        $this->lastname = $lastname;
    }

    public function isEnabled(): bool
    {
        return $this->isEnabled;
    }

    public function setIsEnabled(bool $isEnabled): void
    {
        $this->isEnabled = $isEnabled;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): void
    {
        $this->phone = $phone;
    }

    public function getServiceFonction(): ?string
    {
        return $this->serviceFonction;
    }

    public function setServiceFonction(?string $serviceFonction): void
    {
        $this->serviceFonction = $serviceFonction;
    }

    public function getChannelCode(): ?string
    {
        return $this->channelCode;
    }

    public function setChannelCode(?string $channelCode): void
    {
        $this->channelCode = $channelCode;
    }
}
