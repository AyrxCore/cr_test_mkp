<?php

declare(strict_types=1);

namespace App\State\Provider;

use ApiPlatform\Metadata\CollectionOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Controller\Api\Buyer\ProductApiController;
use App\Factory\ProductFactory;
use App\Service\UpplerProductService;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

readonly class ProductProvider implements ProviderInterface
{
    public function __construct(public UpplerProductService $upplerProductService, private ProductFactory $productFactory, private NormalizerInterface $normalizer)
    {
    }

    /**
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     * @throws \Exception
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        if ($operation instanceof CollectionOperationInterface) {
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
            }
        }

        $product = $this->upplerProductService->findProductById($uriVariables['id']);

        return $this->productFactory->create($product);
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
