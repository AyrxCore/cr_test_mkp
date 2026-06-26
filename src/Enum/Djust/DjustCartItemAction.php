<?php

declare(strict_types=1);

namespace App\Enum\Djust;

enum DjustCartItemAction: string
{
    case ADD_QUANTITY = 'ADD_QUANTITY';
    case REPLACE_QUANTITY = 'REPLACE_QUANTITY';
}
