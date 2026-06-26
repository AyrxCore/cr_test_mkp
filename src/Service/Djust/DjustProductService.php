<?php

declare(strict_types=1);

namespace App\Service\Djust;

use App\Enum\Djust\DjustApiEndpoint;
use App\Enum\Djust\DjustDefaults;
use App\Enum\Djust\DjustIdType;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;

class DjustProductService
{
    public function __construct(
        private readonly LoggerInterface $djustLogger,
        private readonly DjustHttpClientService $djustHttpClient,
    ) {
    }

    public function getProductById(
        string $productId,
        string $idType = DjustIdType::EXTERNAL_ID->value,
        string $locale = DjustDefaults::LOCALE->value,
    ): array {
        $endpoint = \sprintf(DjustApiEndpoint::SHOP_PRODUCT_BY_ID->value, $productId);

        $this->djustLogger->debug('Fetching product from Djust', [
            'productId' => $productId,
            'productIdType' => $idType,
        ]);

        try {
            $response = $this->djustHttpClient->get($endpoint, [
                'productIdType' => $idType,
                'locale' => $locale,
            ]);

            if (empty($response)) {
                $this->djustLogger->warning('Product not found in Djust', ['productId' => $productId]);
                throw new NotFoundHttpException(\sprintf('Product with ID: %s not found', $productId));
            }

            return $response;
        } catch (ClientExceptionInterface $e) {
            $this->djustLogger->warning('Product not found in Djust', [
                'productId' => $productId,
                'error' => $e->getMessage(),
            ]);
            throw new NotFoundHttpException(\sprintf('Product with ID: %s not found', $productId));
        }
    }

    public function getProductOffers(
        string $productId,
        string $productIdType = DjustIdType::EXTERNAL_ID->value,
        string $locale = DjustDefaults::LOCALE->value,
        string $currency = DjustDefaults::CURRENCY->value,
    ): array {
        $endpoint = \sprintf(DjustApiEndpoint::SHOP_PRODUCT_OFFERS->value, $productId);

        $this->djustLogger->debug('Fetching product offers from Djust', [
            'productId' => $productId,
            'productIdType' => $productIdType,
        ]);

        $response = $this->djustHttpClient->get($endpoint, [
            'productIdType' => $productIdType,
            'locale' => $locale,
            'currency' => $currency,
            'size' => 100,
        ]);

        return $response['content'] ?? [];
    }

    public function getFullProduct(
        string $productId,
        string $productIdType = DjustIdType::EXTERNAL_ID->value,
        string $masterIdType = DjustIdType::EXTERNAL_ID->value,
        string $locale = DjustDefaults::LOCALE->value,
    ): array {
        $masterProduct = $this->getProductById($productId, $masterIdType, $locale);
        $offers = $this->getProductOffers($productId, $productIdType, $locale);

        $offersCount = \count($offers);

        if ($offersCount === 0) {
            $this->djustLogger->error('No offers found for product', ['productId' => $productId]);
            throw new NotFoundHttpException(\sprintf('No offers found for product with ID: %s', $productId));
        }

        // Accepter plusieurs offres (une par variant)
        $this->djustLogger->info('Full product fetched', [
            'productId' => $productId,
            'offersCount' => $offersCount,
        ]);

        return [
            'product' => $masterProduct,
            'offers' => $offers,
        ];
    }

    public function getOffersByOfferInventory(string $offerInventoryId): array
    {
        $endpoint = DjustApiEndpoint::SHOP_OFFER_PRICES->value.'?offerInventoryExternalId='.$offerInventoryId;

        return $this->djustHttpClient->get($endpoint);
    }
}
