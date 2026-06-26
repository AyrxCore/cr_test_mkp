<?php

declare(strict_types=1);

namespace App\Service\Djust\Search;

use App\Dto\Djust\DjustSearchParams;
use App\Enum\Djust\DjustApiEndpoint;
use App\Enum\Djust\DjustDefaults;
use App\Mapper\DjustSearchParamsMapper;
use App\Service\Djust\DjustHttpClientService;

class DjustSearchService
{
    public function __construct(
        private readonly DjustHttpClientService $djustHttpClientService,
        private readonly DjustSearchParamsMapper $djustSearchParamsMapper,
    ) {
    }

    public function search(?DjustSearchParams $params = null): array
    {
        if ($params === null) {
            $params = new DjustSearchParams();
        }

        $queryParams = $params->toArray();
        $endpoint = DjustApiEndpoint::SHOP_SEARCH->value;

        return $this->djustHttpClientService->get($endpoint, $queryParams);
    }

    public function searchSplit(array $context): array
    {
        $params = $this->djustSearchParamsMapper->fromContext($context);

        [$allFats, $fatFacets] = $this->fetchAllFats($params);

        $productResult = $this->search(
            $params->withAttributes($this->buildProductAttributes($params)),
        );

        return [
            'accordCadres' => [
                'content' => $allFats,
                'totalElements' => \count($allFats),
            ],
            'products' => $productResult['products'] ?? [],
            'facets' => $this->mergeFacets($fatFacets, $productResult['facets'] ?? []),
        ];
    }

    private function fetchAllFats(DjustSearchParams $params): array
    {
        $fatParams = new DjustSearchParams(
            query: $params->query,
            locale: $params->locale,
            page: '0',
            size: DjustDefaults::SEARCH_PER_PAGE_ACCORD_CADRE->value,
            categoryIds: $params->categoryIds,
            suppliers: $params->suppliers,
            attributes: \array_merge($params->attributes ?? [], ['PRODUCT_TYPE|ACCORD_CADRE']),
            productTags: $params->productTags,
            aggregation: $params->aggregation,
        );

        $firstResult = $this->search($fatParams);
        $totalPages = $firstResult['products']['totalPages'] ?? 1;
        $allFats = $firstResult['products']['content'] ?? [];
        $fatFacets = $firstResult['facets'] ?? [];

        for ($page = 1; $page < $totalPages; ++$page) {
            $result = $this->search($fatParams->withPage((string) $page));
            $allFats = \array_merge($allFats, $result['products']['content'] ?? []);
        }

        \usort($allFats, fn (array $a, array $b) => $this->hasFatPriorityTag($b) <=> $this->hasFatPriorityTag($a));

        return [$allFats, $fatFacets];
    }

    private function hasFatPriorityTag(array $product): bool
    {
        foreach ($product['product']['tags'] ?? [] as $tag) {
            if (($tag['name'] ?? '') === 'fat_priority') {
                return true;
            }
        }

        return false;
    }

    private function buildProductAttributes(DjustSearchParams $params): array
    {
        return \array_values(\array_filter(
            \array_merge($params->attributes ?? [], ['PRODUCT_TYPE|SELLABLE', 'PRODUCT_TYPE|NOT_SELLABLE']),
            fn (string $attr) => !\str_starts_with($attr, 'PRODUCT_TYPE|ACCORD_CADRE'),
        ));
    }

    private function mergeFacets(array $fatFacets, array $productFacets): array
    {
        $merged = $productFacets;

        foreach ($fatFacets as $key => $values) {
            if (!isset($merged[$key])) {
                $merged[$key] = $values;
                continue;
            }

            if (\is_array($values)) {
                $merged[$key] = \array_values(\array_unique(
                    \array_merge($merged[$key], $values),
                    \SORT_REGULAR,
                ));
            }
        }

        return $merged;
    }
}
