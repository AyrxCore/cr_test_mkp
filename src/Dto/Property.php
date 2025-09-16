<?php

declare(strict_types=1);

namespace App\Dto;

use ApiPlatform\Metadata\ApiProperty;

final class Property implements \JsonSerializable
{
    #[ApiProperty(identifier: true)]
    private int $id;
    private ?string $name;
    private mixed $value;
    private ?bool $checked = null;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    /**
     * @param null|mixed $value
     */
    public function setValue(mixed $value): void
    {
        $this->value = $value;
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize(): array
    {
        return \get_object_vars($this);
    }

    public function getChecked(): ?bool
    {
        return $this->checked;
    }

    public function setChecked(?bool $checked): void
    {
        $this->checked = $checked;
    }
}
