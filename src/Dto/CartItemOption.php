<?php

declare(strict_types=1);

namespace App\Dto;

class CartItemOption
{
    private ?string $attributeName = null;
    private ?string $attributeValue = null;

    public function getAttributeName(): ?string
    {
        return $this->attributeName;
    }

    public function setAttributeName(?string $attributeName): self
    {
        $this->attributeName = $attributeName;

        return $this;
    }

    public function getAttributeValue(): ?string
    {
        return $this->attributeValue;
    }

    public function setAttributeValue(?string $attributeValue): self
    {
        $this->attributeValue = $attributeValue;

        return $this;
    }
}
