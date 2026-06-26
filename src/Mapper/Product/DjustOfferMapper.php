<?php

declare(strict_types=1);

namespace App\Mapper\Product;

use App\Dto\Product;
use App\Enum\Djust\DjustCustomField;
use App\Enum\Djust\DjustProductType;
use App\Service\Djust\DjustDataExtractor;

class DjustOfferMapper
{
    public function __construct(
        private readonly DjustDataExtractor $extractor,
    ) {
    }

    public function mapOffersData(Product $product, array $offers, DjustProductType $productType): void
    {
        $this->mapInventoryData($product, $offers[0] ?? []);
        $this->mapAttachments($product, $offers);

        if ($productType === DjustProductType::NOT_SELLABLE && !empty($offers)) {
            $this->extractNotSellableCustomFields($product, $offers[0]);
        }

        // Note: Pricing spécifique au variant est maintenant géré dans DjustVariantMapper
    }

    private function mapInventoryData(Product $product, array $singleOffer): void
    {
        $priceData = $this->extractor->extractSingleOfferPrice($singleOffer);
        $defaultInventory = $priceData['defaultInventory'] ?? null;

        if (!$defaultInventory) {
            return;
        }

        $product->setMinOrderQuantity(\max(1, (int) ($defaultInventory['minOrderQuantity'] ?? 1)));
        $product->setMaxOrderQuantity((int) ($defaultInventory['maxOrderQuantity'] ?? 999) ?: 999);

        // Les caractéristiques techniques viennent UNIQUEMENT des attributeValues du produit master
        // Rien n'est ajouté depuis l'inventory (ni conditionnement, ni customFields)
    }

    private function mapAttachments(Product $product, array $offers): void
    {
        $attachments = $this->extractAllAttachments($offers);

        if (!empty($attachments)) {
            $product->setAttachments($attachments);
        }
    }

    private function extractAllAttachments(array $offers): array
    {
        foreach ($offers as $offer) {
            $customFields = $offer['offerInventory']['customFieldValues'] ?? [];
            $attachments = $this->extractor->extractAttachments($customFields);

            if (!empty($attachments)) {
                return $attachments;
            }
        }

        return [];
    }

    private function extractNotSellableCustomFields(Product $product, array $offer): void
    {
        $offerInventory = $offer['offerInventory'] ?? [];
        $customFieldValues = $offerInventory['customFieldValues'] ?? [];

        if (empty($customFieldValues)) {
            return;
        }

        foreach ($customFieldValues as $customFieldValue) {
            $externalId = $customFieldValue['customField']['externalId'] ?? '';
            $value = $this->extractCustomFieldValueWrapper($customFieldValue['value'] ?? null);

            if ($externalId === DjustCustomField::OFFER_PRICE_TOP_LABEL->value && !empty($value)) {
                $product->setProductTopLabel((string) $value);
            }

            if ($externalId === DjustCustomField::OFFER_PRICE_PRICING_PHRASE->value && !empty($value)) {
                $product->setProductPricingPhrase((string) $value);
            }
        }
    }

    private function extractCustomFieldValueWrapper(mixed $valueWrapper): mixed
    {
        if (\is_array($valueWrapper) && isset($valueWrapper['value'])) {
            return $valueWrapper['value'];
        }

        return $valueWrapper;
    }
}
