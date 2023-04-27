<?php

declare(strict_types=1);

namespace App\Dto;

use ApiPlatform\Core\Annotation\ApiProperty;

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
    public function getValue(): ?string
    {
        return $this->value;
    }

    /**
     * @param mixed|null $value
     */
    public function setValue(mixed $value): void
    {
        $this->value = $value;
    }

    public function jsonSerialize()
    {
        return  get_object_vars($this);
    }

    /**
     * @return bool|null
     */
    public function getChecked(): ?bool
    {
        return $this->checked;
    }

    /**
     * @param bool|null $checked
     */
    public function setChecked(?bool $checked): void
    {
        $this->checked = $checked;
    }
}
