<?php

declare(strict_types=1);

namespace App\Tests;

use App\Dto\Banner;
use App\Dto\ExpertContent;
use App\Tests\Feature\Helper\JsonHelper;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\ResponseInterface;

class MockClientCallback
{
    public function __invoke(string $method, string $url, array $options = []): ResponseInterface
    {
        ['path' => $path] = \parse_url($url);

        if ($path === '/oauth/v2/token') {
            return new MockResponse(JsonHelper::parseJsonDataFile('_mocks/uppler-response/v2/token/gsm-response.json'));
        }

        // Home products
        if ($path === '/v1/buyer/search/product') {
            return $this->getProductsResponse($options);
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

        // match request for a collection resource (ex: /v1/buyer/cart)
        if ($method === 'GET' && \preg_match('/^\/v1\/(?<fileBasePath>[\w_\/-]+)/', $path, $matches)) {
            return new MockResponse(JsonHelper::parseJsonDataFile(
                \sprintf('_mocks/uppler-response/v1/%s/collection.json', $matches['fileBasePath'])
            ));
        }

        return new MockResponse(null);
    }

    private function getProductsResponse(array $options): MockResponse
    {
        return new MockResponse(JsonHelper::parseJsonDataFile('_mocks/uppler-response/v1/buyer/search/products-list.json'));
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
            return new MockResponse(JsonHelper::parseJsonDataFile($basePath.'collection.json'));
        }

        if ($dynamicConfigId === Banner::DYNAMIC_CONFIG_ID) {
            return new MockResponse(JsonHelper::parseJsonDataFile(\sprintf('%sbanner.json', $basePath)));
        }

        return new MockResponse(null);
    }
}
