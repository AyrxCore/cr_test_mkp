<?php

declare(strict_types=1);

namespace App\DataProvider;

use ApiPlatform\Core\DataProvider\ContextAwareCollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Controller\Api\Buyer\ProductApiController;
use App\Dto\Product;
use App\Service\UpplerProductService;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ProductDataProvider implements
    RestrictedDataProviderInterface,
    ContextAwareCollectionDataProviderInterface,
    ItemDataProviderInterface
{
    public function __construct(public UpplerProductService $upplerProductService, private \App\Factory\ProductFactory $productFactory, private NormalizerInterface $normalizer)
    {
    }

    /**
     * @throws \Exception
     */
    public function getCollection(string $resourceClass, string $operationName = null, array $context = []): array
    {
        try {
            $filters = $context['filters'] ?? [];

            $page = $filters['page'] ?? ProductApiController::DEFAULT_PAGE_NUMBER;
            $perPage = $filters['perPage'] ?? ProductApiController::DEFAULT_PER_PAGE;
            $withFilters = $filters['withFilters'] ?? false;

            $apiResponse = $this->upplerProductService->findProducts(
                options: $this->buildSearchOptions($filters),
                page: (int) $page,
                perPage: (int) $perPage
            );

            $products = $this->normalizer->normalize($this->productFactory->createAndAddToCollection($apiResponse['results']), 'json', ['groups' => 'products:get']);
            $result = new ArrayCollection();
            $result->set('results', $products);

            if ($withFilters) {
                $result->set('page', $apiResponse['page']);
                $result->set('resultsCount', $apiResponse['results_count']);
                $result->set('filters', $this->normalizer->normalize($this->productFactory->buildFilter($apiResponse['filters'])));
                $result->set('parameters', $this->productFactory->buildParameter($apiResponse['parameters']));
            }

            return [$result];
        } catch (BadRequestHttpException $badRequestException) {
            return [
                'error' => [
                    'message' => 'An error occurred while retrieving the products.',
                    'details' => $badRequestException->getMessage(),
                ],
            ];
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage());
        }
    }

    public function getItem(string $resourceClass, $id, string $operationName = null, array $context = [])
    {
        $product = $this->upplerProductService->findProductById($id);

        return $this->productFactory->create($product);
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return $resourceClass === Product::class;
    }

    private function buildSearchOptions(array $filters): array
    {
        $options = [];

        if (!empty($filters['name'])) {
            $options['name'] = $filters['name'];
        }

        if (!empty($filters['categories'])) {
            $options['categories'] = [$filters['categories']];
        }

        if (!empty($filters['companies'])) {
            $options['companies'] = [$filters['companies']];
        }

        if (!empty($filters['properties'])) {
            $options['properties'] = [\json_decode($filters['properties'], true)];
        }

        return $options;
    }
}
