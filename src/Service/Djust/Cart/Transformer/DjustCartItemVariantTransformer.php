<?php

declare(strict_types=1);

namespace App\Service\Djust\Cart\Transformer;

use App\Service\Djust\Search\Transformer\DjustSearchPictureTransformer;

class DjustCartItemVariantTransformer
{
    public function __construct(
        private readonly DjustSearchPictureTransformer $pictureTransformer,
    ) {
    }

    public function transform(array $variant, array $product): array
    {
        return [
            'default' => false,
            'id' => $variant['id'] ?? null,
            'skuProduct' => $product['sku'] ?? null,
            'skuVariant' => $variant['sku'] ?? null,
            'name' => $variant['name'] ?? null,
            'description' => $variant['description'] ?? null,
            'mainImageUrl' => $this->pictureTransformer->extractMainImageUrl($variant['pictureUrls'] ?? []),
            'productMediaInfoDTO' => $this->pictureTransformer->extractProductMediaInfoDTO($variant['pictureUrls'] ?? []),
            'productPictures' => $this->pictureTransformer->groupPicturesByMain($variant['pictureUrls'] ?? []),
            'attributeValues' => $variant['attributeValues'] ?? [],
            'gtin' => $variant['ean'] ?? '',
            'ean' => $variant['ean'] ?? '',
            'mpn' => $variant['mpn'] ?? '',
            'externalId' => $variant['externalId'] ?? null,
            'status' => 'ACTIVE',
            'info' => [
                'name' => $variant['name'] ?? null,
                'description' => $variant['description'] ?? null,
                'ean' => $variant['ean'] ?? '',
                'mpn' => $variant['mpn'] ?? '',
                'sku' => $variant['sku'] ?? null,
            ],
        ];
    }
}
