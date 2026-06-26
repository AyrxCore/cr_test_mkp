<?php

declare(strict_types=1);

namespace App\Tests\MockClient;

use App\Dto\Banner;
use App\Dto\ExpertContent;
use App\Tests\Api\Helper\JsonHelper;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\ResponseInterface;

class UpplerMockClientCallback
{
    public function __invoke(string $method, string $url, array $options = []): ResponseInterface
    {
        ['path' => $path, 'query' => $query] = \parse_url($url);

        if ($path === '/oauth/v2/token') {
            return new MockResponse(JsonHelper::parseJsonDataFile('_mocks/uppler-response/v2/token/gsm-response.json'));
        }

        // Home products
        if ($path === '/v1/buyer/search/product') {
            return $this->getProductsResponse($options);
        }

        // Companies
        if ($path === '/v1/buyer/search/company') {
            return $this->getSellersResponse();
        }

        // Dynamic entity
        if ($path === '/v1/administrator/dynamic-entity') {
            return $this->getDynamicEntityResponse($url);
        }

        if ($method === 'POST' && \preg_match('/^\/v1\/buyer\/cart\/\d+\/items/', $path, $matches)) {
            return new MockResponse(info: ['http_code' => Response::HTTP_NO_CONTENT]);
        }

        if ($method === 'PATCH') {
            return new MockResponse(info: ['http_code' => Response::HTTP_NO_CONTENT]);
        }

        // match request for an item resource (ex: /v1/buyer/profile/123)
        // match request for an item's sub-resource (ex: /v1/buyer/cart/123/shipping-method)
        if ($method === 'GET' && \preg_match('/^\/v1\/(?<filePath>[\w_\/-]+\/\d+(?:\/[\w_\/-]+)?)/', $path, $matches)) {
            return new MockResponse(JsonHelper::parseJsonDataFile(
                \sprintf('_mocks/uppler-response/v1/%s.json', $matches['filePath'])
            ));
        }

        // match request for any v1 endpoint with query parameters
        if ($method === 'GET' && !empty($query) && \preg_match('/^\/v1\/(?<filePath>[\w_\/-]+)(?:\?([^#]*))?/', $path, $matches)) {
            \parse_str($query, $queryParams);

            if (!empty($queryParams['criteria'])) {
                $criteriaString = \implode('_', \array_map(
                    fn ($key, $value) => \sprintf('%s_%s', $key, $value),
                    \array_keys($queryParams['criteria']),
                    $queryParams['criteria']
                ));

                return new MockResponse(JsonHelper::parseJsonDataFile(
                    \sprintf('_mocks/uppler-response/v1/%s/%s.json',
                        $matches['filePath'],
                        $criteriaString
                    )
                ));
            }

            return new MockResponse(JsonHelper::parseJsonDataFile(
                \sprintf('_mocks/uppler-response/v1/%s.json', $matches['filePath'])
            ));
        }

        // match request for a collection resource (ex: /v1/buyer/cart)
        if ($method === 'GET' && \preg_match('/^\/v1\/(?<fileBasePath>[\w_\/-]+)/', $path, $matches)) {
            return new MockResponse(JsonHelper::parseJsonDataFile(
                \sprintf('_mocks/uppler-response/v1/%s/collection.json', $matches['fileBasePath'])
            ));
        }

        return new MockResponse();
    }

    private function getProductsResponse(array $options): MockResponse
    {
        return new MockResponse(JsonHelper::parseJsonDataFile('_mocks/uppler-response/v1/buyer/search/products-list.json'));
    }

    private function getSellersResponse(): MockResponse
    {
        return new MockResponse(JsonHelper::parseJsonDataFile('_mocks/uppler-response/v1/buyer/search/sellers-list.json'));
    }

    private function getDynamicEntityResponse(string $url): MockResponse
    {
        $parseUrl = \parse_url($url);
        \parse_str($parseUrl['query'], $queryParams);
        $basePath = '_mocks/uppler-response/v1/administrator/dynamic-entity/';

        if (isset($queryParams['criteria']['slug'])) {
            return new MockResponse(JsonHelper::parseJsonDataFile(\sprintf('%s%s.json', $basePath, $queryParams['criteria']['slug'])));
        }

        $dynamicConfigId = (int) $queryParams['criteria']['dynamic_entity_configuration_id'] ?? null;

        if ($dynamicConfigId === ExpertContent::DYNAMIC_CONFIG_ID) {
            return new MockResponse(JsonHelper::parseJsonDataFile($basePath.'entities.json'));
        }

        if ($dynamicConfigId === Banner::DYNAMIC_CONFIG_ID) {
            return new MockResponse(JsonHelper::parseJsonDataFile(\sprintf('%sbanner.json', $basePath)));
        }

        return new MockResponse();
    }
}
