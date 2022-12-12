<?php

declare(strict_types=1);

namespace App\Dto;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Account;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
#[ApiResource(
    collectionOperations: [
        'create' => [
            "openapi_context" => [
                'summary' => 'Déclarer un account Uppler',
                'description' => 'Permet de créer un account Uppler sur la marketplace 
            en passant toutes les infos de ce compte. Un compte utilisateur est automatiquement créé si nécessaire'
            ],
            "method" => "POST",
            "validate" => true,
        ]
    ],
    itemOperations: ['get']
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

    #[Assert\Email(message: "email required", groups: ["create"])]
    private string $email;

    #[Assert\NotBlank(message: "upplerSubAccountId cannot be null", groups: ["create"])]
    #[Assert\Type("integer", message: "(upplerSubAccountId) Integer required", groups: ["create"])]
    private int $upplerSubAccountId;

    #[Assert\NotBlank(message: "upplerUserId cannot be null", groups: ["create"])]
    #[Assert\Type("integer", message: "(upplerUserId) integer required", groups: ["create"])]
    private int $upplerUserId;

    #[Assert\NotBlank(message: "upplerCompanyId cannot be null")]
    #[Assert\Type("integer", message: "(upplerCompanyId) integer required")]
    private int $upplerCompanyId;

    #[Assert\NotBlank(message: "upplerSubAccountUsername cannot be null")]
    #[Assert\Type("string", message: "(upplerSubAccountUsername) string required")]
    private string $upplerSubAccountUsername;

    #[Assert\NotBlank(message: "upplerSubAccountPassword cannot be null")]
    #[Assert\Type("string", message: "(upplerSubAccountPassword) string required")]
    private string $upplerSubAccountPassword;

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

    public function getUpplerSubAccountId(): int
    {
        return $this->upplerSubAccountId;
    }

    public function setUpplerSubAccountId(int $upplerSubAccountId): self
    {
        $this->upplerSubAccountId = $upplerSubAccountId;

        return $this;
    }

    public function getUpplerUserId(): int
    {
        return $this->upplerUserId;
    }

    public function setUpplerUserId(int $upplerUserId): self
    {
        $this->upplerUserId = $upplerUserId;

        return $this;
    }

    public function getUpplerCompanyId(): int
    {
        return $this->upplerCompanyId;
    }

    public function setUpplerCompanyId(int $upplerCompanyId): self
    {
        $this->upplerCompanyId = $upplerCompanyId;

        return $this;
    }

    public function getUpplerSubAccountUsername(): string
    {
        return $this->upplerSubAccountUsername;
    }

    public function setUpplerSubAccountUsername(string $upplerSubAccountUsername): self
    {
        $this->upplerSubAccountUsername = $upplerSubAccountUsername;

        return $this;
    }

    public function getUpplerSubAccountPassword(): string
    {
        return $this->upplerSubAccountPassword;
    }

    public function setUpplerSubAccountPassword(string $upplerSubAccountPassword): self
    {
        $this->upplerSubAccountPassword = $upplerSubAccountPassword;

        return $this;
    }
}
