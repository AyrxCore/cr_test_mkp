<?php

declare(strict_types=1);

namespace App\Dto\Djust;

use App\Enum\Djust\DjustDefaults;

final class DjustSearchParams
{
    public function __construct(
        public readonly ?string $query = null,
        public readonly string $locale = DjustDefaults::LOCALE->value,
        public readonly string $page = DjustDefaults::SEARCH_PAGE_NUMBER->value,
        public readonly string $size = DjustDefaults::SEARCH_PER_PAGE_PRODUCT->value,
        public readonly ?string $categoryIds = null,
        public readonly ?array $suppliers = null,
        public readonly ?array $attributes = null,
        public readonly ?string $productTags = null,
        public readonly ?string $aggregation = null,
    ) {
    }

    public function withPage(string $page): self
    {
        return $this->with(['page' => $page]);
    }

    public function withAttributes(?array $attributes): self
    {
        return $this->with(['attributes' => $attributes]);
    }

    public function toArray(): array
    {
        $data = [
            'query' => $this->query,
            'locale' => $this->locale,
            'page' => $this->page,
            'size' => $this->size,
            'categoryIds' => $this->categoryIds,
            'suppliers' => $this->suppliers,
            'attributes' => $this->attributes,
            'productTags' => $this->productTags,
            'aggregation' => $this->aggregation,
        ];

        return \array_filter($data, static fn ($value) => $value !== null && $value !== '' && $value !== []);
    }

    private function with(array $overrides): self
    {
        return new self(
            query: $overrides['query'] ?? $this->query,
            locale: $overrides['locale'] ?? $this->locale,
            page: $overrides['page'] ?? $this->page,
            size: $overrides['size'] ?? $this->size,
            categoryIds: $overrides['categoryIds'] ?? $this->categoryIds,
            suppliers: $overrides['suppliers'] ?? $this->suppliers,
            attributes: $overrides['attributes'] ?? $this->attributes,
            productTags: $overrides['productTags'] ?? $this->productTags,
            aggregation: $overrides['aggregation'] ?? $this->aggregation,
        );
    }
}
