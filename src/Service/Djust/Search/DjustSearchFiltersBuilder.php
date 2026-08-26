<?php

declare(strict_types=1);

namespace App\Service\Djust\Search;

use App\Dto\Seller;
use App\Factory\CategoryFactory;
use App\Factory\SellerFactory;
use App\Service\Djust\DjustCategoryService;

class DjustSearchFiltersBuilder
{
    public function __construct(
        private readonly CategoryFactory $categoryFactory,
        private readonly DjustCategoryService $djustCategoryService,
        private readonly SellerFactory $sellerFactory,
    ) {
    }

    public function buildFilter(array $remoteFilters, array $requestedFilters = []): array
    {
        $filters = [];

        if (!empty($remoteFilters['suppliers'])) {
            $filters['sellers'] = $this->sellerFactory->createAndAddToCollection($remoteFilters['suppliers']);
        }

        if (!empty($remoteFilters['navigations'])) {
            $navigationIds = \array_column($remoteFilters['navigations'], 'id');
            $categories = $this->filterCategoriesByIds($this->djustCategoryService->getAvailableCategories(), $navigationIds);
            if (!empty($categories)) {
                $filters['categories'] = $this->categoryFactory->createAndAddToCollection($categories);
            }
        }

        if (!empty($remoteFilters['attributes'])) {
            $filters['properties'] = $remoteFilters['attributes'];
        }

        return $this->restrictSellers($filters, $requestedFilters);
    }

    private function restrictSellers(array $filters, array $requestedFilters): array
    {
        if (!empty($requestedFilters['sellers']) && !empty($filters['sellers'])) {
            $requested = $requestedFilters['sellers'];
            $restricted = \array_values(\array_filter(
                $filters['sellers'],
                static fn (Seller $seller) => \in_array($seller->getExternalId(), $requested, true)
                    || \in_array($seller->getId(), $requested, true),
            ));

            if (empty($restricted)) {
                unset($filters['sellers']);
            } else {
                $filters['sellers'] = $restricted;
            }
        }

        return $filters;
    }

    private function filterCategoriesByIds(array $categories, array $navigationIds): array
    {
        $filtered = [];

        foreach ($categories as $category) {
            // Check if this category matches
            $categoryMatches = \in_array($category['id'], $navigationIds, true);

            // Filter children recursively regardless of whether parent matches
            $filteredChildren = [];
            if (!empty($category['childrenCategories'])) {
                $filteredChildren = $this->filterCategoriesByIds($category['childrenCategories'], $navigationIds);
            }

            // Include the category if it matches OR if it has matching children
            if ($categoryMatches || !empty($filteredChildren)) {
                if (!empty($filteredChildren)) {
                    $category['childrenCategories'] = $filteredChildren;
                } else {
                    unset($category['childrenCategories']);
                }
                $filtered[] = $category;
            }
        }

        return $filtered;
    }
}
