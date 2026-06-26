<?php

declare(strict_types=1);

namespace App\Service\Djust\Cart\Transformer;
use App\Service\Djust\Search\Transformer\DjustSearchNavigationTransformer;

class DjustCartProductTransformer
{
    public function __construct(
        private readonly DjustSearchNavigationTransformer $navigationTransformer,
    ) {
    }

    public function transform(array $cartItem): array
    {
        $productDto = $cartItem['orderLogisticLineProductDto'] ?? [];
        $variantDto = $cartItem['orderLogisticLineProductVariantDto'] ?? [];

        $attributes = $productDto['productAttributeValues'] ?? [];
        $transformedAttributes = [];
        foreach ($attributes as $attr) {
            $transformedAttributes[] = [
                'attribute' => [
                    'id' => $attr['attributeId'] ?? null,
                    'name' => $attr['attributeName'] ?? [],
                    'type' => $attr['attributeType'] ?? 'TEXT',
                    'externalId' => $attr['attributeExternalId'] ?? null,
                ],
                'value' => $attr['attributeValue'] ?? null,
            ];
        }

        $navigations = $productDto['navigationCategories'] ?? [];

        return [
            'id' => $cartItem['id'] ?? null,
            'quantity' => $cartItem['quantity'] ?? null,
            'sku' => $productDto['sku'] ?? null,
            'name' => $this->extractLocalizedField($productDto, 'name'),
            'description' => $this->extractLocalizedField($productDto, 'description'),
            'productStatus' => 'ACTIVE',
            'brand' => $productDto['brand'] ?? null,
            'attributeValues' => $transformedAttributes,
            'productUnit' => $this->transformProductUnit($productDto['productUnit'] ?? null),
            'images' => [],
            'productPictures' => $variantDto['productMediaInfoDTOS'] ?? [],
            'navigationCategories' => $this->navigationTransformer->transform($navigations),
            'tags' => $productDto['tags'] ?? [],
            'djustProductUuid' => $productDto['djustProductUuid'] ?? null,
            'mpn' => $variantDto['mpn'] ?? $productDto['mpn'] ?? null,
            'gtin' => $variantDto['gtin'] ?? $productDto['gtin'] ?? null,
            'externalId' => $productDto['externalId'] ?? null,
            'externalSource' => $productDto['externalSource'] ?? 'CLIENT',
            'info' => [
                'name' => $productDto['name'] ?? null,
                'description' => $productDto['description'] ?? null,
                'brand' => $productDto['brand'] ?? null,
                'sku' => $productDto['sku'] ?? null,
                'unit' => $this->transformProductUnit($productDto['productUnit'] ?? null),
            ],
            'isBundle' => false,
        ];
    }

    private function transformProductUnit(?string $unit): ?array
    {
        if ($unit === null) {
            return null;
        }

        return [
            'type' => 'ITEM',
            'unit' => $unit,
            'id' => null, // Non présent dans cartItem.json
        ];
    }

    private function extractLocalizedField(array $data, string $fieldName): array
    {
        $value = $data[$fieldName] ?? null;

        if ($value === null) {
            return [];
        }

        if (\is_array($value)) {
            return $value;
        }

        return [
            'fr-FR' => $value,
            'FR' => $value,
        ];
    }
}
