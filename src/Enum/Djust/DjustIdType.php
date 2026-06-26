<?php

declare(strict_types=1);

namespace App\Enum\Djust;

enum DjustIdType: string
{
    case ID = 'ID';
    case SKU = 'SKU';
    case DJUST_ID = 'DJUST_ID';
    case EXTERNAL_ID = 'EXTERNAL_ID';
}
