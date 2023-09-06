<?php

declare(strict_types=1);

namespace App\Dto;

use ApiPlatform\Core\Annotation\ApiResource;

#[ApiResource(
    collectionOperations: [
        'get' => [
            'path' => '/categories',
        ],
    ],
)]
final class Category
{
    private ?int $id = null;

    private ?int $parentId;
    private ?string $name;

    private ?string $image;

    private ?int $productCount;

    private array $children;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getParentId(): ?int
    {
        return $this->parentId;
    }

    public function setParentId(?int $parentId): void
    {
        $this->parentId = $parentId;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): void
    {
        $this->image = $image;
    }

    public function getProductCount(): ?int
    {
        return $this->productCount;
    }

    public function setProductCount(?int $productCount): void
    {
        $this->productCount = $productCount;
    }

    public function getChildren(): array
    {
        return $this->children;
    }

    public function setChildren(array $children): void
    {
        $this->children = $children;
    }

    public function hydrate(\stdClass $data): self
    {
        $this->id = $data->id;
        $this->name = $data->name;
        $this->image = $data->image;
        $this->parentId = $data->parent;
        $this->productCount = $data->count;
        $children = [];

        if (!empty($data->child)) {
            foreach ($data->child as $childData) {
                $child = new self();
                $children[] = $child->hydrate($childData);
            }
        }

        $this->children = $children;

        return $this;
    }
}
