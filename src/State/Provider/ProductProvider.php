<?php

declare(strict_types=1);

namespace App\State\Provider;

use ApiPlatform\Metadata\CollectionOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Dto\Product;
use App\Enum\Djust\DjustProductType;
use App\Factory\DjustProductFactory;
use App\Mapper\DjustSearchParamsMapper;
use App\Service\Djust\DjustProductService;
use App\Service\Djust\Search\DjustSearchFiltersBuilder;
use App\Service\Djust\Search\DjustSearchService;
use App\Service\Djust\Search\Transformer\DjustSearchResultTransformer;

readonly class ProductProvider implements ProviderInterface
{
    public function __construct(
        private DjustSearchParamsMapper $djustSearchParamsMapper,
        private DjustSearchService $djustSearchService,
        private DjustProductService $djustProductService,
        private DjustProductFactory $djustProductFactory,
        private DjustSearchFiltersBuilder $djustSearchFiltersBuilder,
        private DjustSearchResultTransformer $searchResultTransformer,
    ) {
    }

    /**
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     * @throws \Exception
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        if ($operation instanceof CollectionOperationInterface) {
            $filters = $context['filters'] ?? [];
            $filters['sellers'] = isset($filters['sellers']) ? (array) $filters['sellers'] : [];

            if (!empty($filters['splitSearch'])) {
                $search = $this->djustSearchService->searchSplit($context);
                $accordCadres = $search['accordCadres']['content'] ?? [];
                $mappedAccordCadres = \array_map(
                    fn (array $item) => $this->searchResultTransformer->transformSearchResultItem($item),
                    $accordCadres,
                );
                $accordCadresProducts = $this->filterDisplayableAccordCadres(
                    $this->djustProductFactory->createAndAddToCollection($mappedAccordCadres)
                );

                return \array_merge(
                    $this->buildSearchResponse($search, $filters),
                    [
                        'accordCadres' => $accordCadresProducts,
                        'accordCadresCount' => \count($accordCadresProducts),
                    ],
                );
            }

            $params = $this->djustSearchParamsMapper->fromContext($context);
            $search = $this->djustSearchService->search($params);

            return $this->buildSearchResponse($search, $filters);
        }

        $externalId = (string) $uriVariables['id'];

        $fullProduct = $this->djustProductService->getFullProduct($externalId);

        return $this->djustProductFactory->create($fullProduct);
    }

    private function buildSearchResponse(array $search, array $requestedFilters = []): array
    {
        $products = $search['products']['content'] ?? [];
        $mappedProducts = \array_map(
            fn (array $item) => $this->searchResultTransformer->transformSearchResultItem($item),
            $products,
        );
        $productDtos = $this->filterDisplayableProducts(
            $this->djustProductFactory->createAndAddToCollection($mappedProducts)
        );

        $filters = $this->djustSearchFiltersBuilder->buildFilter($search['facets'] ?? [], $requestedFilters);

        return [
            'results' => $productDtos,
            'resultsCount' => $search['products']['totalElements'] ?? \count($productDtos),
            'page'         => $search['products']['pageable']['pageNumber'] ?? 0,
            'totalPages'   => $search['products']['totalPages'] ?? 1,
            'filters'      => $filters,
        ];
    }

    /**
     * @param Product[] $products
     *
     * @return Product[]
     */
    private function filterDisplayableProducts(array $products): array
    {
        return $this->filterProducts($products, fn (Product $p): bool => !$this->isInvalidAccordCadreProduct($p));
    }

    /**
     * @param Product[] $products
     *
     * @return Product[]
     */
    private function filterDisplayableAccordCadres(array $products): array
    {
        return $this->filterProducts($products, fn (Product $p): bool => !$this->isInvalidAccordCadrePayload($p));
    }

    /**
     * @param Product[]        $products
     * @param callable(Product): bool $predicate
     *
     * @return Product[]
     */
    private function filterProducts(array $products, callable $predicate): array
    {
        return \array_values(\array_filter($products, $predicate));
    }

    private function isInvalidAccordCadreProduct(Product $product): bool
    {
        if ($product->getProductType() !== DjustProductType::ACCORD_CADRE->value) {
            return false;
        }

        return $this->isInvalidAccordCadrePayload($product);
    }

    private function isInvalidAccordCadrePayload(Product $product): bool
    {
        return $product->getAccordCadreContent() === null
            || $product->getAccordId() === null
            || $product->getTarifId() === null;
    }
}
