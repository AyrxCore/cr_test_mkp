<?php

declare(strict_types=1);

namespace App\Service\Djust\Product;

use App\Dto\Product;
use App\Enum\Djust\DjustCustomField;
use App\Enum\Djust\DjustProductTag;
use App\Service\Djust\DjustDataExtractor;

class DjustPropertyFilter
{
    public function __construct(
        private readonly DjustDataExtractor $extractor,
    ) {
    }

    public function mapProperties(Product $product, array $masterProduct): void
    {
        $properties = [];

        foreach ($masterProduct['attributeValues'] ?? [] as $attrValue) {
            $externalId = $attrValue['attribute']['externalId'] ?? '';

            if ($this->isExcludedExternalId($externalId)) {
                continue;
            }

            $attrName = $this->extractor->getLocalizedValue($attrValue['attribute']['name'] ?? []);

            // Exclure la TVA par son nom (ce n'est pas un customField, c'est un attribut standard)
            if (strtolower(trim($attrName)) === 'tva') {
                continue;
            }

            $rawValue = $attrValue['value'] ?? '';
            $value = \is_array($rawValue) ? $this->extractor->getLocalizedValue($rawValue) : (string) $rawValue;

            if (!empty($attrName) && !empty($value)) {
                $properties[$attrName] = $value;
            }
        }

        if (!empty($masterProduct['brand'])) {
            $properties['Marque'] = $masterProduct['brand'];
        }

        $product->setProperties($properties);
    }

    public function mapTags(Product $product, array $masterProduct): void
    {
        $tags = [];

        foreach ($masterProduct['tags'] ?? [] as $tag) {
            $tagName = \trim($tag['name'] ?? '');
            if (!empty($tagName) && $this->isWhitelistedTag($tagName)) {
                $tags[] = $tagName;
            }
        }

        $product->setTags($tags);
    }

    public function isWhitelistedTag(string $tagName): bool
    {
        return \in_array($tagName, DjustProductTag::whitelist(), true);
    }

    public function isExcludedExternalId(string $externalId): bool
    {
        return match ($externalId) {
            DjustCustomField::PRODUCT_FORM_WITH_MESSAGE->value,
            DjustCustomField::PRODUCT_TYPE->value,
            DjustCustomField::PRODUCT_ACCORD_ID->value,
            DjustCustomField::PRODUCT_SHIPPING_CATEGORY->value,
            DjustCustomField::OFFER_ATTACHMENT->value,
            DjustCustomField::OFFER_PRICE_TOP_LABEL->value,
            DjustCustomField::OFFER_PRICE_PRICING_PHRASE->value,
            DjustCustomField::OFFER_TARIF_ID->value,
            DjustCustomField::OFFER_TVA->value => true,

            default => false,
        };
    }

    public function shouldShowFormWithMessage(array $masterProduct): bool
    {
        foreach ($masterProduct['attributeValues'] ?? [] as $attrValue) {
            $externalId = $attrValue['attribute']['externalId'] ?? '';

            if ($externalId === DjustCustomField::PRODUCT_FORM_WITH_MESSAGE->value) {
                $value = $attrValue['value'] ?? '';
                $stringValue = \is_array($value) ? ($value[0] ?? '') : (string) $value;

                return \strtolower(\trim($stringValue)) === 'oui';
            }
        }

        return false;
    }
}
