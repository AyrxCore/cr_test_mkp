<?php

declare(strict_types=1);

namespace App\Service\Djust\Search\Transformer;

class DjustSearchSupplierTransformer
{
    public function transform(array $supplier): array
    {
        return [
            'id' => $supplier['id'] ?? null,
            'externalId' => $supplier['externalId'] ?? null,
            'name' => $supplier['name'] ?? null,
            'supplierRating' => null,
            'returnPolicy' => '',
            'customFieldValues' => [],
        ];
    }
}
