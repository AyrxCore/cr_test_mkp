<?php

declare(strict_types=1);

namespace App\Helper;

class UpplerHelper
{
    public static function getOrderNumber(mixed $upplerResponse): ?string
    {
        foreach ($upplerResponse['numbers'] as $number) {
            if ($number['type'] === 'order') {
                return $number['number'];
            }
        }

        return null;
    }

    public static function formatPrice(int $price): float
    {
        return \round($price * 0.01, 2);
    }
}
