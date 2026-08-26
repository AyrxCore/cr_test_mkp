<?php

declare(strict_types=1);

namespace App\Enum\Djust;

enum DjustProductTag: string
{
    case MADE_IN_FRANCE = 'made_in_france';
    case ACHAT_DURABLE = 'achat_durable';

    public static function whitelist(): array
    {
        return \array_column(self::cases(), 'value');
    }
}
