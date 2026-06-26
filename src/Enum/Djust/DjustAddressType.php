<?php

declare(strict_types=1);

namespace App\Enum\Djust;

enum DjustAddressType: string
{
    case SHIPPING = 'SHIPPING';
    case BILLING = 'BILLING';
}
