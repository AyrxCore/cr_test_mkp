<?php

declare(strict_types=1);

use App\Enum\Djust\DjustApiEndpoint;
use App\Enum\Djust\DjustDefaults;
use App\Enum\Djust\DjustIdType;
use App\Service\Djust\DjustDataExtractor;
use App\Service\Djust\DjustHttpClientService;
use App\Service\Djust\DjustProductService;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;

\beforeEach(function () {
    $this->logger = Mockery::mock(LoggerInterface::class);
    $this->httpClient = Mockery::mock(DjustHttpClientService::class);
    $this->dataExtractor = Mockery::mock(DjustDataExtractor::class);

    $this->service = new DjustProductService(
        $this->logger,
        $this->httpClient,
        $this->dataExtractor
    );
});

\afterEach(function () {
    Mockery::close();
});

\it('fetches product by id successfully', function () {
    $productId = '12345';
    $expectedResponse = ['id' => $productId, 'name' => 'Test Product'];

    $this->logger->shouldReceive('debug')
        ->once()
        ->with('Fetching product from Djust', [
            'productId' => $productId,
            'productIdType' => DjustIdType::EXTERNAL_ID->value,
        ]);

    $this->httpClient->shouldReceive('get')
        ->once()
        ->with(
            \sprintf(DjustApiEndpoint::SHOP_PRODUCT_BY_ID->value, $productId),
            [
                'productIdType' => DjustIdType::EXTERNAL_ID->value,
                'locale' => DjustDefaults::LOCALE->value,
            ]
        )
        ->andReturn($expectedResponse);

    $result = $this->service->getProductById($productId);

    \expect($result)->toBe($expectedResponse);
})->group('djust-product-service');

\it('throws NotFoundHttpException when product response is empty', function () {
    $productId = '99999';

    $this->logger->shouldReceive('debug')->once();
    $this->logger->shouldReceive('warning')
        ->once()
        ->with('Product not found in Djust', ['productId' => $productId]);

    $this->httpClient->shouldReceive('get')
        ->once()
        ->andReturn([]);

    $this->service->getProductById($productId);
})->group('djust-product-service')
  ->throws(NotFoundHttpException::class, 'Product with ID: 99999 not found');

\it('throws NotFoundHttpException when client exception occurs', function () {
    $productId = '12345';
    $clientException = new class('HTTP 404 Not Found') extends \Exception implements ClientExceptionInterface {
        public function getResponse(): Symfony\Contracts\HttpClient\ResponseInterface
        {
            return Mockery::mock(Symfony\Contracts\HttpClient\ResponseInterface::class);
        }
    };

    $this->logger->shouldReceive('debug')->once();
    $this->logger->shouldReceive('warning')
        ->once()
        ->with('Product not found in Djust', [
            'productId' => $productId,
            'error' => 'HTTP 404 Not Found',
        ]);

    $this->httpClient->shouldReceive('get')
        ->once()
        ->andThrow($clientException);

    $this->service->getProductById($productId);
})->group('djust-product-service')
  ->throws(NotFoundHttpException::class, 'Product with ID: 12345 not found');

\it('fetches product offers successfully', function () {
    $productId = '12345';
    $expectedOffers = [
        ['id' => 1, 'price' => 100],
        ['id' => 2, 'price' => 200],
    ];
    $response = ['content' => $expectedOffers];

    $this->logger->shouldReceive('debug')
        ->once()
        ->with('Fetching product offers from Djust', [
            'productId' => $productId,
            'productIdType' => DjustIdType::EXTERNAL_ID->value,
        ]);

    $this->httpClient->shouldReceive('get')
        ->once()
        ->with(
            \sprintf(DjustApiEndpoint::SHOP_PRODUCT_OFFERS->value, $productId),
            [
                'productIdType' => DjustIdType::EXTERNAL_ID->value,
                'locale' => DjustDefaults::LOCALE->value,
                'currency' => DjustDefaults::CURRENCY->value,
                'size' => 100,
            ]
        )
        ->andReturn($response);

    $result = $this->service->getProductOffers($productId);

    \expect($result)->toBe($expectedOffers);
})->group('djust-product-service');

\it('returns empty array when offers have no content', function () {
    $productId = '12345';

    $this->logger->shouldReceive('debug')->once();

    $this->httpClient->shouldReceive('get')
        ->once()
        ->andReturn([]);

    $result = $this->service->getProductOffers($productId);

    \expect($result)->toBe([]);
})->group('djust-product-service');

\it('fetches full product successfully with one offer', function () {
    $productId = '12345';
    $masterProduct = ['id' => $productId, 'name' => 'Test Product'];
    $offers = [['id' => 1, 'price' => 100]];

    $this->logger->shouldReceive('debug')->twice();
    $this->logger->shouldReceive('info')
        ->once()
        ->with('Full product fetched', [
            'productId' => $productId,
            'offersCount' => 1,
        ]);

    $this->httpClient->shouldReceive('get')
        ->once()
        ->with(
            \sprintf(DjustApiEndpoint::SHOP_PRODUCT_BY_ID->value, $productId),
            Mockery::any()
        )
        ->andReturn($masterProduct);

    $this->httpClient->shouldReceive('get')
        ->once()
        ->with(
            \sprintf(DjustApiEndpoint::SHOP_PRODUCT_OFFERS->value, $productId),
            Mockery::any()
        )
        ->andReturn(['content' => $offers]);

    $result = $this->service->getFullProduct($productId);

    \expect($result)->toHaveKeys(['product', 'offers']);
    \expect($result['product'])->toBe($masterProduct);
    \expect($result['offers'])->toBe($offers);
})->group('djust-product-service');

\it('throws NotFoundHttpException when no offers found', function () {
    $productId = '12345';
    $masterProduct = ['id' => $productId, 'name' => 'Test Product'];

    $this->logger->shouldReceive('debug')->twice();
    $this->logger->shouldReceive('error')
        ->once()
        ->with('No offers found for product', ['productId' => $productId]);

    $this->httpClient->shouldReceive('get')
        ->once()
        ->with(
            \sprintf(DjustApiEndpoint::SHOP_PRODUCT_BY_ID->value, $productId),
            Mockery::any()
        )
        ->andReturn($masterProduct);

    $this->httpClient->shouldReceive('get')
        ->once()
        ->with(
            \sprintf(DjustApiEndpoint::SHOP_PRODUCT_OFFERS->value, $productId),
            Mockery::any()
        )
        ->andReturn(['content' => []]);

    $this->service->getFullProduct($productId);
})->group('djust-product-service')
  ->throws(NotFoundHttpException::class, 'No offers found for product with ID: 12345');

\it('fetches full product successfully with multiple offers (variants)', function () {
    $productId = '12345';
    $masterProduct = ['id' => $productId, 'name' => 'Test Product'];
    $offers = [
        ['id' => 1, 'price' => 100],
        ['id' => 2, 'price' => 200],
        ['id' => 3, 'price' => 150],
    ];

    $this->logger->shouldReceive('debug')->twice();
    $this->logger->shouldReceive('info')
        ->once()
        ->with('Full product fetched', [
            'productId' => $productId,
            'offersCount' => 3,
        ]);

    $this->httpClient->shouldReceive('get')
        ->once()
        ->with(
            \sprintf(DjustApiEndpoint::SHOP_PRODUCT_BY_ID->value, $productId),
            Mockery::any()
        )
        ->andReturn($masterProduct);

    $this->httpClient->shouldReceive('get')
        ->once()
        ->with(
            \sprintf(DjustApiEndpoint::SHOP_PRODUCT_OFFERS->value, $productId),
            Mockery::any()
        )
        ->andReturn(['content' => $offers]);

    $result = $this->service->getFullProduct($productId);

    \expect($result)->toHaveKeys(['product', 'offers']);
    \expect($result['product'])->toBe($masterProduct);
    \expect($result['offers'])->toBe($offers);
    \expect(\count($result['offers']))->toBe(3);
})->group('djust-product-service');

\it('uses custom parameters when provided', function () {
    $productId = '12345';
    $customIdType = 'SKU';
    $customLocale = 'en-US';

    $this->logger->shouldReceive('debug')
        ->once()
        ->with('Fetching product from Djust', [
            'productId' => $productId,
            'productIdType' => $customIdType,
        ]);

    $this->httpClient->shouldReceive('get')
        ->once()
        ->with(
            \sprintf(DjustApiEndpoint::SHOP_PRODUCT_BY_ID->value, $productId),
            [
                'productIdType' => $customIdType,
                'locale' => $customLocale,
            ]
        )
        ->andReturn(['id' => $productId]);

    $result = $this->service->getProductById($productId, $customIdType, $customLocale);

    \expect($result)->toHaveKey('id');
})->group('djust-product-service');
