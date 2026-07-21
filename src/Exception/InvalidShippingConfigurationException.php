<?php

declare(strict_types=1);

namespace App\Exception;

use RuntimeException;

class InvalidShippingConfigurationException extends RuntimeException
{
    public static function emptyLevels(string $calculatorType): self
    {
        return new self(sprintf('Shipping configuration for type "%s" has no levels defined', $calculatorType));
    }

    public static function missingPercentage(): self
    {
        return new self('Shipping configuration for type "PERCENTAGE" is missing percentage field');
    }

    public static function emptyWeights(): self
    {
        return new self('Shipping configuration for type "WEIGHT" has no weights defined');
    }

    public static function missingRequiredField(string $calculatorType, string $field): self
    {
        return new self(sprintf('Shipping configuration for type "%s" is missing required field: %s', $calculatorType, $field));
    }
}
