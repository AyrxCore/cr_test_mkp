<?php

declare(strict_types=1);

namespace App\Dto\SemanticButton;

final class SemanticButton
{
    private int $id;

    private ?string $label = null;

    private ?string $search = null;

    private ?string $sectionTitle = null;

    private int $position = 0;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
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

    public function getPosition(): int
    {
        return $this->position;
    }

    public function setPosition(int $position): void
    {
        $this->position = $position;
    }
}
