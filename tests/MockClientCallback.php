<?php

declare(strict_types=1);

namespace App\Tests;

use App\Controller\Api\Buyer\ProductApiController;
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
            return $this->getHomeProductsResponse($options);
        }

        if ($method === 'POST' && \preg_match('/^\/v1\/buyer\/cart\/\d+\/items/', $path, $matches)) {
            return new MockResponse(info: ['http_code' => Response::HTTP_NO_CONTENT]);
        }

        if ($method === 'PATCH') {
            return new MockResponse(info: ['http_code' => Response::HTTP_NO_CONTENT]);
        }

//        // Cart
//        if ($path === '/v1/buyer/cart') {
//            return new MockResponse(JsonHelper::parseJsonDataFile('_mocks/uppler-response/v1/buyer/cart/collection.json'));
//        }

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

    private function getHomeProductsResponse(array $options): MockResponse
    {
        $requestBody = $options['body'];

        if (\str_contains($requestBody, \json_encode(ProductApiController::HOME_TOP_VENTE_PROPERTY))) {
            return new MockResponse(JsonHelper::parseJsonDataFile('_mocks/uppler-response/v1/buyer/search/products-top-vente.json'));
        }

        if (\str_contains($requestBody, \json_encode(ProductApiController::HOME_ACCORD_CADRE_PROPERTY))) {
            return new MockResponse(JsonHelper::parseJsonDataFile('_mocks/uppler-response/v1/buyer/search/products-accord-cadre.json'));
        }

        return new MockResponse(JsonHelper::parseJsonDataFile('_mocks/uppler-response/v1/buyer/search/products-selection.json'));
    }
}
