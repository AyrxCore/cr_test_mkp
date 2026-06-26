<?php

declare(strict_types=1);

namespace App\Service\Djust\Product;

use App\Enum\Djust\DjustCustomField;
use App\Enum\Djust\DjustProductType;
use App\Service\Djust\DjustDataExtractor;

class DjustProductTypeExtractor
{
    public function __construct(
        private readonly DjustDataExtractor $extractor,
    ) {
    }

    public function extract(array $masterProduct): DjustProductType
    {
        $attrValue = $this->extractor->findAttributeByExternalId(
            $masterProduct['attributeValues'] ?? [],
            DjustCustomField::PRODUCT_TYPE->value
        );

        if ($attrValue === null) {
            return DjustProductType::SELLABLE;
        }

        $value = $attrValue['value'] ?? null;

        $typeValue = \is_array($value) && !empty($value) ? \reset($value) : $value;

        if (!empty($typeValue)) {
            $normalized = \strtoupper((string) $typeValue);
            return DjustProductType::tryFrom($normalized) ?? DjustProductType::SELLABLE;
        }

        return DjustProductType::SELLABLE;
    }
}

