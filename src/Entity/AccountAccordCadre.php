<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\AccountAccordCadreRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: AccountAccordCadreRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[ApiResource(
    itemOperations: [
        'get',
        'update' => [
            "openapi_context" => [
                'summary' => 'Mettre à jour le status de l\'accord cadre par rapport à l\'account',
            ],
            "method" => "PUT",
            "validate" => true,
        ]
    ],
)]
class AccountAccordCadre implements \JsonSerializable
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private ?Uuid $id = null;

    #[ORM\Column(nullable: false)]
    private ?string $accountId = null;

    #[ORM\Column]
    private ?int $accordCadreId = null;

    #[ORM\Column(length: 15)]
    private ?string $status = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\PrePersist]
    public function onPrePersist(): void
    {
        $this->updatedAt = new \DateTime('now');
        $this->createdAt = new \DateTime('now');
    }

    #[ORM\PreUpdate]
    public function onPreUpdate(): void
    {
        $this->updatedAt = new \DateTime('now');
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    /**
     * @return User|null
     */
    public function getAccountId(): ?string
    {
        return $this->accountId;
    }

    /**
     * @param string|null $accountId
     */
    public function setAccountId(?string $accountId): void
    {
        $this->accountId = $accountId;
    }

    public function getAccordCadreId(): ?int
    {
        return $this->accordCadreId;
    }

    public function setAccordCadreId(int $accordCadreId): self
    {
        $this->accordCadreId = $accordCadreId;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function jsonSerialize()
    {
        return  get_object_vars($this);
    }
}
