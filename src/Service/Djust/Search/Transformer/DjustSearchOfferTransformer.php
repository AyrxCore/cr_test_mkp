<?php

declare(strict_types=1);

namespace App\Service\Djust\Search\Transformer;

class DjustSearchOfferTransformer
{
    public function __construct(
        private readonly DjustSearchVariantTransformer $variantTransformer,
    ) {
    }

    public function transform(array $searchItem): array
    {
        $product = $searchItem['product'] ?? [];
        $variant = $searchItem['variant'] ?? [];
        $offer = $searchItem['offer'] ?? [];

        return [
            'id' => $offer['id'] ?? null,
            'quantityPerItem' => (float) ($offer['quantityPerItem'] ?? 1),
            'stock' => $offer['stock'] ?? 0,
            'externalSource' => 'CLIENT',
            'status' => 'ACTIVE',
            'currency' => $offer['currency'] ?? 'EUR',
            'packingType' => $offer['packingType'] ?? 'UNIT',
            'productUnit' => $offer['productUnit'] ?? $product['productUnit'] ?? 'item',
            'variant' => $this->variantTransformer->transform($variant, $product),
            'maxOrderQuantity' => $offer['maxOrderQuantity'] ?? null,
            'minOrderQuantity' => $offer['minOrderQuantity'] ?? 0,
            'minStockAlert' => $offer['minStockAlert'] ?? null,
            'minShippingPrice' => $offer['minShippingPrice'] ?? null,
            'minShippingPriceAdditional' => null,
            'minShippingType' => null,
            'minShippingZone' => null,
            'leadTimeToShip' => $offer['leadTimeToShip'] ?? null,
            'customFieldValues' => $this->transformCustomFields($offer['customFields'] ?? []),
            'brand' => $product['brand'] ?? null,
        ];
    }

    private function transformCustomFields(array $customFields): array
    {
        if (empty($customFields)) {
            return [];
        }

        $result = [];

        foreach ($customFields as $field) {
            // Si déjà au bon format, on le garde
            if (isset($field['customField']) && isset($field['value'])) {
                $result[] = $field;
                continue;
            }

            // Sinon, on construit le format attendu
            $customFieldId = $field['id'] ?? $field['customFieldId'] ?? null;
            $externalId = $field['externalId'] ?? null;
            $name = $field['name'] ?? [];
            $type = $field['type'] ?? 'TEXT';
            $value = $field['value'] ?? null;

            if ($customFieldId === null && $externalId === null) {
                continue;
            }

            $customFieldObject = [
                'id' => $customFieldId,
                'externalId' => $externalId,
                'name' => \is_array($name) ? $name : ['FR' => $name],
                'type' => $type,
            ];

            $result[] = [
                'customField' => $customFieldObject,
                'value' => [
                    'customField' => \array_merge($customFieldObject, [
                        'externalSource' => 'CLIENT',
                        'mandatory' => $field['mandatory'] ?? false,
                        'status' => $field['status'] ?? 'ACTIVE',
                        'sealedTarget' => 'OFFER',
                        'role' => null,
                        'indexable' => $field['indexable'] ?? false,
                        'faceted' => $field['faceted'] ?? false,
                        'searchable' => $field['searchable'] ?? false,
                        'sortable' => $field['sortable'] ?? false,
                    ]),
                    'value' => $value,
                    'type' => $type,
                ],
            ];
        }

        return $result;
    }
}
