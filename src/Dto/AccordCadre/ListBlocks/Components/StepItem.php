<?php

declare(strict_types=1);

namespace App\Dto\AccordCadre\ListBlocks\Components;

use Symfony\Component\Serializer\Annotation\Groups;

final class StepItem
{
    #[Groups(['products:get', 'product:get'])]
    private string $title;
    #[Groups(['products:get', 'product:get'])]
    private string $description;

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }
}
