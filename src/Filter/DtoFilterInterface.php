<?php

declare(strict_types=1);

namespace App\Filter;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('app.dto_filter')]
interface DtoFilterInterface
{
    public function shouldInclude(FilterableInterface $object): bool;

    public function getName(): string;
}
