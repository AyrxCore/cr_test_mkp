<?php

declare(strict_types=1);

namespace App\Dto;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Put;
use App\State\Provider\CategoryProvider;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    operations: [
        new Get(),
        new Put(),
        new Patch(),
        new Delete(),
        new GetCollection(
            uriTemplate: '/categories'
        ),
    ],
    provider: CategoryProvider::class
)]
final class Category
{
    #[Groups(['products:get', 'product:get', 'map:read'])]
    private ?string $id = null;

    #[Groups(['products:get', 'product:get', 'map:read'])]
    private ?string $externalId = null;

    private ?string $parentId;

    #[Groups(['products:get', 'product:get', 'map:read'])]
    private ?string $name;

    #[Groups(['products:get', 'product:get', 'map:read'])]
    private array $children = [];

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(string $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getExternalId(): ?string
    {
        return $this->externalId;
    }

    public function setExternalId(?string $externalId): void
    {
        $this->externalId = $externalId;
    }

    public function getParentId(): ?string
    {
        return $this->parentId;
    }

    public function setParentId(?string $parentId): void
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

    public function getChildren(): array
    {
        return $this->children;
    }

    public function setChildren(array $children): void
    {
        $this->children = $children;
    }
}
