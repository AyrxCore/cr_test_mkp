<?php

declare(strict_types=1);

namespace App\Service\Djust;

use App\Dto\Djust\DjustSearchParams;
use App\Enum\Djust\DjustApiEndpoint;
use App\Service\Djust\Search\DjustSearchService;

class DjustCategoryService
{
    public function __construct(
        private readonly DjustHttpClientService $djustHttpClientService,
        private readonly DjustSearchService $djustSearchService,
    ) {
    }

    public function getAvailableCategories(?DjustSearchParams $params = null): array
    {
        $allCategories = $this->djustHttpClientService->get(
            DjustApiEndpoint::SHOP_NAVIGATION_CATEGORY_ONLINE->value
        );

        $accountCategoryIds = $this->getSearchCategories($params);

        return $this->filterCategories($allCategories, $accountCategoryIds);
    }

    private function filterCategories(array $categories, array $allowedIds, ?string $parentId = null): array
    {
        $filtered = [];

        foreach ($categories as $category) {
            if (\in_array($category['id'], $allowedIds, true)) {
                if ($parentId !== null) {
                    $category['parentId'] = $parentId;
                }

                if (!empty($category['childrenCategories'])) {
                    $category['childrenCategories'] = $this->filterCategories(
                        $category['childrenCategories'],
                        $allowedIds,
                        $category['id']
                    );
                }

                $filtered[] = $category;
            }
        }

        return $filtered;
    }

    private function getSearchCategories(?DjustSearchParams $params = null): array
    {
        $search = $this->djustSearchService->search($params);

        return \array_map(static fn ($item) => $item['id'], $search['facets']['navigations'] ?? []);
    }
}
