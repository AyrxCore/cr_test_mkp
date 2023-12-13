<?php

declare(strict_types=1);

namespace App\Helper;

class ColorHelper
{
    public static function hexToCssRgb(string $hex, int|float $opacity = 1): string
    {
        [$red, $green, $blue] = \sscanf(\strtolower(\str_replace('#', '', $hex)), '%2x%2x%2x');

        return \sprintf('rgb(%d %d %d / %f)', $red, $green, $blue, $opacity);
    }
}
