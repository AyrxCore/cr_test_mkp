<?php

declare(strict_types=1);

namespace App\Dto\AccordCadre\ListBlocks;

use App\Dto\AccordCadre\AccordCadreContentBlockInterface;
use App\Dto\AccordCadre\ListBlocks\Components\StepItem;
use Symfony\Component\Serializer\Annotation\Groups;

final class StepsBlockContent implements AccordCadreContentBlockInterface
{
    #[Groups(['product:get'])]
    private string $componentName;
    #[Groups(['product:get'])]
    private string $title;
    /** @var StepItem[] */
    #[Groups(['product:get'])]
    private ?array $stepItems = [];

    public function getComponentName(): string
    {
        return $this->componentName;
    }

    public function setComponentName(string $componentName): self
    {
        $this->componentName = $componentName;

        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getStepItems(): ?array
    {
        return $this->stepItems;
    }

    public function setStepItems(?array $stepItems): self
    {
        $this->stepItems = $stepItems;

        return $this;
    }

    public function addStepItem(StepItem $stepItem): self
    {
        $this->stepItems[] = $stepItem;

        return $this;
    }
}
