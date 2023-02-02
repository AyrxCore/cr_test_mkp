<?php

declare(strict_types=1);

namespace App\Dto;

use ApiPlatform\Core\Annotation\ApiProperty;


final class Seller implements \JsonSerializable
{
    #[ApiProperty(identifier: true)]
    private ?int $id = null;

    private ?string $name;
    private ?string $corporateName;
    private ?string $description;
    private ?string $avatar;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string|null $description
     */
    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return string|null
     */
    public function getCorporateName(): ?string
    {
        return $this->corporateName;
    }

    /**
     * @param string|null $corporateName
     */
    public function setCorporateName(?string $corporateName): void
    {
        $this->corporateName = $corporateName;
    }

    /**
     * @return string|null
     */
    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    /**
     * @param string|null $avatar
     */
    public function setAvatar(?string $avatar): void
    {
        $this->avatar = $avatar;
    }

    public function jsonSerialize()
    {
        return  get_object_vars($this);
    }
}
