<?php

declare(strict_types=1);

namespace App\Service\Djust\Cart\Transformer;

use App\Service\Djust\Search\Transformer\DjustSearchVariantTransformer;

class DjustCartOfferTransformer
{
    public function __construct(
        private readonly DjustCartItemVariantTransformer $djustCartItemVariantTransformer,
    ) {
    }

    public function transform(array $cartItem): array
    {
        $inventorySnapshot = $cartItem['offerInventorySnapshotDto'] ?? [];
        $productDto = $cartItem['orderLogisticLineProductDto'] ?? [];
        $variantDto = $cartItem['orderLogisticLineProductVariantDto'] ?? [];

        return [
            'id' => $inventorySnapshot['offerInventoryExternalId'] ?? null,
            'quantityPerItem' => (float) ($inventorySnapshot['quantityPerItem'] ?? 1),
            'stock' => 100, // Non présent, on met une valeur par défaut élevée
            'externalSource' => $inventorySnapshot['externalSource'] ?? 'CLIENT',
            'status' => 'ACTIVE',
            'currency' => $inventorySnapshot['currency'] ?? 'EUR',
            'packingType' => $inventorySnapshot['packingType'] ?? 'UNIT',
            'productUnit' => $productDto['productUnit'] ?? 'item',
            'variant' => $this->djustCartItemVariantTransformer->transform($this->mapVariantDtoToCartItemFormat($variantDto), $this->mapProductDtoToSearchFormat($productDto)),
            'maxOrderQuantity' => $inventorySnapshot['maxOrderQuantity'] ?? null,
            'minOrderQuantity' => $inventorySnapshot['minOrderQuantity'] ?? 0,
            'minStockAlert' => null,
            'minShippingPrice' => null,
            'minShippingPriceAdditional' => null,
            'minShippingType' => null,
            'minShippingZone' => null,
            'leadTimeToShip' => null,
            'customFieldValues' => $this->transformCustomFields($inventorySnapshot['customFieldValueSnapshots'] ?? []),
            'brand' => $productDto['brand'] ?? null,
        ];
    }

    private function mapVariantDtoToCartItemFormat(array $variantDto): array
    {
        return [
            'id' => $variantDto['externalId'] ?? null,
            'sku' => $variantDto['sku'] ?? null,
            'name' => $variantDto['name'] ?? null,
            'description' => $variantDto['description'] ?? null,
            'mainImageUrl' => $variantDto['mainImageUrl'] ?? null,
            'pictureUrls' => $variantDto['productMediaInfoDTOS'] ?? [],
            'mpn' => $variantDto['mpn'] ?? null,
            'ean' => $variantDto['ean'] ?? null,
            'attributeValues' => $this->extractOptions($variantDto['attributeValues'] ?? []),
        ];
    }

    private function extractOptions(array $attributeValues): array
    {
        $options = [];

        foreach ($attributeValues as $attr) {
            $attributeName = $this->getLocalizedName($attr['attributeName'] ?? []);
            $attributeValue = $this->parseAttributeValue($attr['attributeValue'] ?? null);

            if ($attributeName !== null && $attributeValue !== null) {
                $options[] = [
                    'attribute' => [
                        'id' => $attr['attributeId'] ?? null,
                        'name' => $attributeName,
                        'type' => $attr['attributeType'] ?? 'TEXT',
                        'externalId' => $attr['attributeExternalId'] ?? null,
                    ],
                    'value' => $attributeValue,
                ];
            }
        }

        return $options;
    }

    private function getLocalizedName(array $names): ?string
    {
        return $names['fr-FR'] ?? null;
    }

    private function parseAttributeValue(?string $value): mixed
    {
        if ($value === null) {
            return null;
        }

        $decoded = \json_decode($value, true);

        if (\json_last_error() === \JSON_ERROR_NONE) {
            if (\is_array($decoded) && \count($decoded) === 1) {
                $firstElement = \reset($decoded);
                if (\is_array($firstElement) && isset($firstElement['label'])) {
                    return $firstElement['label'];
                }

                return $firstElement;
            }

            return $decoded;
        }

        return $value;
    }

    private function mapProductDtoToSearchFormat(array $productDto): array
    {
        return [
            'brand' => $productDto['brand'] ?? null,
            'productUnit' => $productDto['productUnit'] ?? null,
        ];
    }

    private function transformCustomFields(array $customFieldSnapshots): array
    {
        $result = [];

        foreach ($customFieldSnapshots as $snapshot) {
            $fieldDto = $snapshot['customFieldSnapshotDto'] ?? [];

            $customFieldId = $fieldDto['externalId'] ?? null;
            $externalId = $fieldDto['externalId'] ?? null;
            $names = $fieldDto['names'] ?? [];
            $type = $fieldDto['type'] ?? 'TEXT';
            $value = $snapshot['value'] ?? null;

            if ($externalId === null) {
                continue;
            }

            $customFieldObject = [
                'id' => $customFieldId,
                'externalId' => $externalId,
                'name' => $names,
                'type' => $type,
            ];

            $result[] = [
                'customField' => $customFieldObject,
                'value' => [
                    'customField' => \array_merge($customFieldObject, [
                        'externalSource' => $fieldDto['externalSource'] ?? 'CLIENT',
                        'mandatory' => $fieldDto['mandatory'] ?? false,
                        'status' => $fieldDto['status'] ?? 'ACTIVE',
                        'sealedTarget' => $fieldDto['sealedTarget'] ?? 'OFFER',
                        'role' => $fieldDto['role'] ?? null,
                    ]),
                    'value' => $value,
                    'type' => $type,
                ],
            ];
        }

        return $result;
    }
}
