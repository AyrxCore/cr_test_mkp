<?php

declare(strict_types=1);

namespace App\Dto\AccordCadre;

interface AccordCadreContentBlockInterface
{
    public function getComponentName(): string;

    public function setComponentName(string $componentName): self;
}
