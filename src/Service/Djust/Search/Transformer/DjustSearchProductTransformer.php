<?php

declare(strict_types=1);

namespace App\Service\Djust\Search\Transformer;

class DjustSearchProductTransformer
{
    public function __construct(
        private readonly DjustSearchAttributeTransformer $attributeTransformer,
        private readonly DjustSearchNavigationTransformer $navigationTransformer,
        private readonly DjustSearchPictureTransformer $pictureTransformer,
    ) {
    }

    public function transform(array $searchItem): array
    {
        $product = $searchItem['product'] ?? [];
        $variant = $searchItem['variant'] ?? [];
        $attributes = $searchItem['attributes'] ?? [];
        $navigations = $searchItem['navigations'] ?? [];

        return [
            'id' => $product['id'] ?? null,
            'sku' => $product['sku'] ?? null,
            'name' => $this->extractLocalizedField($product, 'name'),
            'description' => $this->extractLocalizedField($product, 'description'),
            'productStatus' => 'ACTIVE',
            'brand' => $product['brand'] ?? null,
            'attributeValues' => $this->attributeTransformer->transformToAttributeValues($attributes),
            'productUnit' => $this->transformProductUnit($product['productUnit'] ?? $product['unit'] ?? null),
            'imageLinks' => [],
            'productPictures' => $this->pictureTransformer->groupPicturesByMain($variant['pictureUrls'] ?? []),
            'navigationCategories' => $this->navigationTransformer->transform($navigations),
            'tags' => $product['tags'] ?? [],
            'djustProductUuid' => $product['externalId'] ?? null,
            'mpn' => $variant['mpn'] ?? $product['mpn'] ?? null,
            'gtin' => $variant['ean'] ?? $product['ean'] ?? null,
            'externalId' => $product['externalId'] ?? null,
            'externalSource' => 'CLIENT',
            'info' => [
                'name' => $product['name'] ?? null,
                'description' => $product['description'] ?? null,
                'brand' => $product['brand'] ?? null,
                'sku' => $product['sku'] ?? null,
                'unit' => $this->transformProductUnit($product['productUnit'] ?? $product['unit'] ?? null),
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
            'id' => '134',
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
