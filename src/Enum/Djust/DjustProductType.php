<?php

declare(strict_types=1);

namespace App\Enum\Djust;

enum DjustProductType: string
{
    case SELLABLE = 'SELLABLE';
    case NOT_SELLABLE = 'NOT_SELLABLE';
    case ACCORD_CADRE = 'ACCORD_CADRE';
}
