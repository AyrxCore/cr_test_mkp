<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UpplerProductService extends AbstractUpplerService
{
    public function findProducts(
        array $options,
        array $expands = [],
        int $page = 1,
        int $perPage = 10,
    ): array {
        $expands = empty($expands) ? ['price', 'properties', 'company', 'images'] : $expands;
        $fields = ['id', 'name', 'images', 'properties', 'company', 'variants', 'slug', 'price', 'price_reference', 'description'];
        $urlRequest = \sprintf('v1/buyer/search/product?page=%d&perPage=%d', $page, $perPage);

        foreach ($fields as $field) {
            $urlRequest .= \sprintf('&fields[product][]=%s', $field);
        }

        if (!empty($expands)) {
            foreach ($expands as $expand) {
                $urlRequest .= \sprintf('&expand[]=%s', $expand);
            }
        }

        $res = $this->request(
            'POST',
            $urlRequest,
            [
                'json' => $options,
            ]
        );

        if ($res->getStatusCode() !== Response::HTTP_OK) {
            throw new BadRequestHttpException();
        }

        return \json_decode($res->getContent(), true);
    }

    public function findProductById(int $productId = null, array $filters = [], ?string $accountId = null): array
    {
        $filters = empty($filters) ? ['price', 'properties', 'variants', 'company'] : $filters;
        $urlFilters = null;

        if (!empty($filters)) {
            foreach ($filters as $filter) {
                $urlFilters .= $urlFilters === null ? '?expand[]='.$filter : '&expand[]='.$filter;
            }
        }

        $res = $this->request(
            'GET',
            'v1/buyer/product/'.$productId.$urlFilters
        );

        if ($res->getStatusCode() !== Response::HTTP_OK) {
            throw new NotFoundHttpException('Product with ID: '.$productId.' not found');
        }

        return \json_decode($res->getContent(), true);
    }

    public function findVariantById(int $variantId = null)
    {
        $res = $this->request(
            'GET',
            'v1/buyer/variant/'.$variantId.'?expand[]=price'
        );

        if ($res->getStatusCode() !== Response::HTTP_OK) {
            throw new NotFoundHttpException('Variant not found');
        }

        return \json_decode($res->getContent(), true);
    }

    public function findAllCategories(int $page = 1, int $perPage = 1): array
    {
        $res = $this->request(
            'POST',
            'v1/buyer/search/product?page='.$page.'&perPage='.$perPage,
        );

        if ($res->getStatusCode() !== Response::HTTP_OK) {
            throw new NotFoundHttpException('Category not found');
        }

        $remoteData = \json_decode($res->getContent(), true);

        return $remoteData['filters']['category'];
    }
}
