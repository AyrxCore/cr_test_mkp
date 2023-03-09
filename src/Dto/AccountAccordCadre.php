<?php

declare(strict_types=1);

namespace App\Dto;

use Symfony\Component\Uid\Uuid;


final class AccountAccordCadre implements \JsonSerializable
{

    public const PROCESS_STATUS_NOT_ACTIVATED = 'NOT_ACTIVATED';

    public const PROCESS_STATUS_PENDING = 'PENDING';

    public const PROCESS_STATUS_ACTIVATED = 'ACTIVATED';

    private ?Uuid $id = null;

    private ?string $accountId = null;

    private ?int $accordCadreId = null;

    private ?Uuid $accordId = null;

    private ?string $status = null;

    private ?\DateTimeInterface $createdAt = null;

    private ?\DateTimeInterface $updatedAt = null;

    /**
     * @return \Symfony\Component\Uid\Uuid|null
     */
    public function getId(): ?Uuid
    {
        return $this->id;
    }

    /**
     * @param  \Symfony\Component\Uid\Uuid|null  $id
     */
    public function setId(?Uuid $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string|null
     */
    public function getAccountId(): ?string
    {
        return $this->accountId;
    }

    /**
     * @param  string|null  $accountId
     */
    public function setAccountId(?string $accountId): void
    {
        $this->accountId = $accountId;
    }

    /**
     * @return int|null
     */
    public function getAccordCadreId(): ?int
    {
        return $this->accordCadreId;
    }

    /**
     * @param  int|null  $accordCadreId
     */
    public function setAccordCadreId(?int $accordCadreId): void
    {
        $this->accordCadreId = $accordCadreId;
    }

    /**
     * @return \Symfony\Component\Uid\Uuid|null
     */
    public function getAccordId(): ?Uuid
    {
        return $this->accordId;
    }

    /**
     * @param  \Symfony\Component\Uid\Uuid|null  $accordId
     */
    public function setAccordId(?Uuid $accordId): void
    {
        $this->accordId = $accordId;
    }

    /**
     * @return string|null
     */
    public function getStatus(): ?string
    {
        return $this->status;
    }

    /**
     * @param  string|null  $status
     */
    public function setStatus(?string $status): void
    {
        $this->status = $status;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getCreatedAt(): ?\DateTimeInterface
    {
        //        return $this->createdAt;
        return new \DateTime('now');
    }

    /**
     * @param  \DateTimeInterface|null  $createdAt
     */
    public function setCreatedAt(?\DateTimeInterface $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getUpdatedAt(): ?\DateTimeInterface
    {
        //        return $this->updatedAt;
        return new \DateTime('now');
    }

    /**
     * @param  \DateTimeInterface|null  $updatedAt
     */
    public function setUpdatedAt(?\DateTimeInterface $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }


    public function jsonSerialize()
    {
        return get_object_vars($this);
    }

}
