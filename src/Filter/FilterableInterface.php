<?php

declare(strict_types=1);

namespace App\Filter;

interface FilterableInterface
{
    public function getFilterCriteria(): array;
}
