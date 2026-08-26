<?php

declare(strict_types=1);

namespace App\Mapper;

use App\Dto\Djust\DjustSearchParams;
use App\Enum\Djust\DjustDefaults;
use Psr\Log\LoggerInterface;

class DjustSearchParamsMapper
{
    public function __construct(private readonly ?LoggerInterface $logger = null)
    {
    }

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

    private function formatAttributes(string|array|null $attributes): ?array
    {
        if ($attributes === null) {
            return null;
        }

        $items = \is_array($attributes) ? $attributes : [\json_decode($attributes, true)];

        $formatted = [];
        foreach ($items as $item) {
            if (!\is_array($item) || !isset($item['property_id'], $item['value'])) {
                $this->logger?->debug('Rejected malformed Djust attribute item', ['item' => $item]);
                continue;
            }
            $formatted[] = $item['property_id'].'|'.$item['value'];
        }

        return $formatted !== [] ? $formatted : null;
    }
}
