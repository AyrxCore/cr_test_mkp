<?php

declare(strict_types=1);

namespace App\Mapper;

use App\Dto\Djust\DjustSearchParams;
use App\Enum\Djust\DjustDefaults;

class DjustSearchParamsMapper
{
    public function fromContext(array $context): DjustSearchParams
    {
        $aggregation = $context['aggregation'] ?? 'PRODUCT';
        $filters = $context['filters'] ?? [];

        return new DjustSearchParams(
            query: $filters['name'] ?? null,
            locale: $filters['locale'] ?? DjustDefaults::LOCALE->value,
            page: $filters['page'] ?? DjustDefaults::SEARCH_PAGE_NUMBER->value,
            size: $filters['perPage'] ?? DjustDefaults::SEARCH_PER_PAGE_PRODUCT->value,
            categoryIds: $filters['categories'] ?? null,
            suppliers: isset($filters['sellers']) ? (array) $filters['sellers'] : null,
            attributes: $this->formatAttributes($filters['properties'] ?? null),
            productTags: $filters['productTags'] ?? null,
            aggregation: $aggregation,
        );
    }

    private function formatAttributes(null|string|array $attributes): ?array
    {
        if ($attributes === null) {
            return null;
        }

        $items = \is_array($attributes) ? $attributes : [\json_decode($attributes, true)];

        return \array_map(function (mixed $item) use ($attributes): string {
            if (!\is_array($item) || !isset($item['property_id'], $item['value'])) {
                throw new \InvalidArgumentException('Invalid attributes format: '.\json_encode($attributes));
            }

            return $item['property_id'].'|'.$item['value'];
        }, $items);
    }
}
