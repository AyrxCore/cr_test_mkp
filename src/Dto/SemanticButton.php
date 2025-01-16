<?php

declare(strict_types=1);

namespace App\Dto;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\State\Provider\SemanticButtonsProvider;

#[ApiResource(
    operations: [
        new GetCollection(
            uriTemplate: '/semantic_buttons',
        ),
    ],
    provider: SemanticButtonsProvider::class
)]
class SemanticButton
{
    #[ApiProperty(identifier: true)]
    private ?int $id = null;

    private ?string $label = null;
    private ?string $search = null;
    private ?string $sectionTitle = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(?string $label): void
    {
        $this->label = $label;
    }

    public function getSearch(): ?string
    {
        return $this->search;
    }

    public function setSearch(?string $search): void
    {
        $this->search = $search;
    }

    public function getSectionTitle(): ?string
    {
        return $this->sectionTitle;
    }

    public function setSectionTitle(?string $sectionTitle): void
    {
        $this->sectionTitle = $sectionTitle;
    }
}
