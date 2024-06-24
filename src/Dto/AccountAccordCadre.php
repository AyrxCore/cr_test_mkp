<?php

declare(strict_types=1);

namespace App\Dto;

use Symfony\Component\Uid\Uuid;

final class AccountAccordCadre implements \JsonSerializable
{
    public const string PROCESS_STATUS_NOT_ACTIVATED = 'NOT_ACTIVATED';

    public const string PROCESS_STATUS_PENDING = 'PENDING';

    public const string PROCESS_STATUS_ACTIVATED = 'ACTIVATED';

    private ?Uuid $id = null;

    private ?string $accountId = null;

    private ?int $accordCadreId = null;

    private ?Uuid $accordId = null;

    private ?string $status = null;

    private ?\DateTimeInterface $createdAt = null;

    private ?\DateTimeInterface $updatedAt = null;

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function setId(?Uuid $id): void
    {
        $this->id = $id;
    }

    public function getAccountId(): ?string
    {
        return $this->accountId;
    }

    public function setAccountId(?string $accountId): void
    {
        $this->accountId = $accountId;
    }

    public function getAccordCadreId(): ?int
    {
        return $this->accordCadreId;
    }

    public function setAccordCadreId(?int $accordCadreId): void
    {
        $this->accordCadreId = $accordCadreId;
    }

    public function getAccordId(): ?Uuid
    {
        return $this->accordId;
    }

    public function setAccordId(?Uuid $accordId): void
    {
        $this->accordId = $accordId;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): void
    {
        $this->status = $status;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeInterface $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return \get_object_vars($this);
    }
}
