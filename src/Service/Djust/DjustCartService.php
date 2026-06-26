<?php

declare(strict_types=1);

namespace App\Service\Djust;

use App\Enum\Djust\DjustApiEndpoint;
use App\Enum\Djust\DjustCartItemAction;
use App\Enum\Djust\DjustCustomField;
use App\Enum\Djust\DjustDefaults;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class DjustCartService
{
    public const string SHIPPING_TYPE_STANDARD = 'STANDARD';

    public function __construct(
        private DjustHttpClientService $djustHttpClient,
        private DjustStoreViewHeadersBuilder $storeViewHeadersBuilder,
        private LoggerInterface $logger,
    ) {
    }

    public function getCart(): ?array
    {
        try {
            $responseContent = $this->djustHttpClient->get(
                DjustApiEndpoint::SHOP_COMMERCIAL_ORDERS->value,
                [
                    'isValidated' => 'false',
                    'locale' => DjustDefaults::LOCALE->value,
                ],
                $this->storeViewHeadersBuilder->build()
            );
        } catch (\Exception $e) {
            throw new BadRequestHttpException('Erreur lors de la récupération du panier');
        }

        $djustCarts = $responseContent['content'];

        if (empty($djustCarts)) {
            $djustCart = $this->createCart();
        } else {
            \usort($djustCarts, static fn (array $a, array $b): int => ($b['productCount'] ?? 0) <=> ($a['productCount'] ?? 0));
            $djustCart = $djustCarts[0];
        }

        return $djustCart;
    }

    public function createCart(): array
    {
        return $this->djustHttpClient->post(
            DjustApiEndpoint::SHOP_COMMERCIAL_ORDERS->value,
            [],
            $this->storeViewHeadersBuilder->build(),
        );
    }

    public function updateCartItems(string $cartId, array $cartItems): array
    {
        $endpoint = \sprintf(DjustApiEndpoint::SHOP_COMMERCIAL_ORDERS_ITEMS->value, $cartId);
        $cartItemsActions = [
            'updateOrderCommercialLines' => [],
        ];

        foreach ($cartItems as $cartItem) {
            $cartItemAction = [];
            $cartItemAction['updateAction'] = DjustCartItemAction::from($cartItem->getAction())->value;
            $cartItemAction['id'] = $cartItem->getOfferPriceId();
            $cartItemAction['quantity'] = (int) $cartItem->getQuantity();
            $cartItemsActions['updateOrderCommercialLines'][] = $cartItemAction;
        }

        return $this->djustHttpClient->put($endpoint, $cartItemsActions);
    }

    public function syncCommercialOrder(string $cartId): array
    {
        $endpoint = \sprintf(DjustApiEndpoint::SHOP_COMMERCIAL_ORDER_SYNC->value, $cartId);

        try {
            $response = $this->djustHttpClient->put($endpoint, []);
            $this->logger->info('[DjustCart] Sync OK.', [
                'cartId' => $cartId,
                'response' => $response,
            ]);

            return $response;
        } catch (\Exception $e) {
            $this->logger->warning('[DjustCart] Sync call failed — non-blocking.', [
                'cartId' => $cartId,
                'error' => $e->getMessage(),
            ]);

            return [];
        }
    }

    public function deleteCartLines(string $cartId, array $lineIds): void
    {
        if (empty($lineIds)) {
            return;
        }

        $endpoint = \sprintf(DjustApiEndpoint::SHOP_COMMERCIAL_ORDERS_ITEMS->value, $cartId);

        $body = \array_map(
            static fn (string $id): array => ['offerPriceId' => $id],
            $lineIds,
        );

        try {
            $this->djustHttpClient->deleteWithBody($endpoint, $body);
            $this->logger->info('[DjustCart] deleteCartLines OK.', [
                'cartId' => $cartId,
                'deletedIds' => $lineIds,
            ]);
        } catch (\Exception $e) {
            $this->logger->warning('[DjustCart] deleteCartLines failed — line(s) may already be gone.', [
                'cartId' => $cartId,
                'ids' => $lineIds,
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function syncAndCleanCart(string $cartId): array
    {
        $syncResponse = $this->syncCommercialOrder($cartId);
        $blockingIds = $this->extractBlockingLineIds($syncResponse);

        if (!empty($blockingIds)) {
            $this->deleteCartLines($cartId, $blockingIds);
        }

        return $blockingIds;
    }

    public function updateCartBillingAddress(string $cartId, string $billingAddressId): array
    {
        $endpoint = \sprintf(DjustApiEndpoint::SHOP_COMMERCIAL_ORDER_BILLING_ADDRESS->value, $cartId);

        return $this->djustHttpClient->put($endpoint, [
            'billingAddressId' => $billingAddressId,
        ]);
    }

    public function updateCartShippingAddress(string $cartId, string $shippingAddressId): array
    {
        $endpoint = \sprintf(DjustApiEndpoint::SHOP_COMMERCIAL_ORDER_SHIPPING_ADDRESS->value, $cartId);

        return $this->djustHttpClient->put($endpoint, [
            'shippingAddressId' => $shippingAddressId,
            'shippingType' => self::SHIPPING_TYPE_STANDARD,
        ]);
    }

    public function getPaymentMethods(string $cartId): array
    {
        try {
            $response = $this->djustHttpClient->get(DjustApiEndpoint::SHOP_PAYMENTS_METHODS->value, [
                'reference' => $cartId,
                'countryCode' => DjustDefaults::COUNTRY_CODE->value,
                'locale' => DjustDefaults::LOCALE->value,
            ]);
        } catch (\Exception $e) {
            throw new BadRequestHttpException(
                \sprintf('Erreur lors de la récupération des moyens de paiement : %s', $e->getMessage()),
            );
        }

        try {
            $paymentMethods = \json_decode($response['adyenPaymentMethods'] ?? '{}', true, flags: \JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new BadRequestHttpException(
                \sprintf('Réponse invalide lors de la récupération des moyens de paiement : %s', $e->getMessage()),
            );
        }

        return [
            'paymentMethods' => \is_array($paymentMethods['paymentMethods'] ?? null) ? $paymentMethods['paymentMethods'] : [],
            'storedPaymentMethods' => \is_array($paymentMethods['storedPaymentMethods'] ?? null) ? $paymentMethods['storedPaymentMethods'] : [],
            'enableCreditCardStorage' => $response['enableCreditCardStorage'] ?? false,
        ];
    }

    public function initiatePayment(array $payload, string $clientIp = ''): array
    {
        if (empty($payload['customerUserIP']) && $clientIp !== '') {
            $payload['customerUserIP'] = $clientIp;
        }

        try {
            return $this->djustHttpClient->post(DjustApiEndpoint::SHOP_PAYMENTS_INITIATE->value, $payload, $this->storeViewHeadersBuilder->build());
        } catch (\Exception $e) {
            throw new BadRequestHttpException(
                \sprintf('Erreur lors de l\'initialisation du paiement : %s', $e->getMessage()),
            );
        }
    }

    public function submitPaymentDetails(array $details): array
    {
        try {
            return $this->djustHttpClient->post(DjustApiEndpoint::SHOP_PAYMENTS_DETAILS->value, $details, $this->storeViewHeadersBuilder->build());
        } catch (\Exception $e) {
            throw new BadRequestHttpException(
                \sprintf('Erreur lors de la soumission des détails de paiement : %s', $e->getMessage()),
            );
        }
    }

    public function updateAllLogisticOrdersCustomFields(
        array $djustCart,
        ?string $siret,
        ?string $email,
        ?string $phone,
    ): void {
        foreach ($djustCart['orderLogistics'] ?? [] as $orderLogistic) {
            $logisticOrderId = $orderLogistic['reference'] ?? null;
            if ($logisticOrderId === null) {
                continue;
            }

            $this->updateLogisticOrderCustomFields(
                (string) $logisticOrderId,
                $siret,
                $email,
                $phone,
            );
        }
    }

    public function updateAllLogisticOrdersLinesEcoTax(array $djustCart): void
    {
        foreach ($djustCart['orderLogistics'] ?? [] as $orderLogistic) {
            $logisticOrderId = $orderLogistic['reference'] ?? null;
            if ($logisticOrderId === null) {
                continue;
            }

            $linesPayload = $this->buildEcoTaxLinesPayload($orderLogistic['lines'] ?? []);

            if (empty($linesPayload)) {
                continue;
            }

            $this->updateLogisticOrderLinesEcoTax((string) $logisticOrderId, $linesPayload);
        }
    }

    public function updateLogisticOrderCustomFields(
        string $logisticOrderId,
        ?string $siret,
        ?string $email,
        ?string $phone,
    ): void {
        $customFieldValues = [
            [
                'customFieldId' => DjustCustomField::ORDER_SIRET_CLIENT->value,
                'customFieldValue' => $siret ?? '',
            ],
            [
                'customFieldId' => DjustCustomField::ORDER_EMAIL_ADDRESS->value,
                'customFieldValue' => $email ?? '',
            ],
            [
                'customFieldId' => DjustCustomField::ORDER_TEL_NUMBER->value,
                'customFieldValue' => $phone ?? '',
            ],
        ];

        $endpoint = \sprintf(DjustApiEndpoint::SHOP_LOGISTIC_ORDER->value, $logisticOrderId);
        try {
            $this->djustHttpClient->patch($endpoint, ['customFieldValues' => $customFieldValues], $this->storeViewHeadersBuilder->build());
        } catch (\Exception $e) {
            $this->logger->warning('[DjustCart] Mise à jour des custom fields du logistic order échouée — non bloquant.', [
                'logisticOrderId' => $logisticOrderId,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function extractBlockingLineIds(array $syncResponse): array
    {
        $ids = [];

        foreach ($syncResponse as $item) {
            if (\is_array($item) && ($item['blocked'] ?? false) === true && isset($item['id'])) {
                $ids[] = $item['id'];
                $this->logger->info('[DjustCart] Blocking line detected.', [
                    'id' => $item['id'],
                    'code' => $item['code'] ?? null,
                    'detail' => $item['detail'] ?? null,
                ]);
            }
        }

        return $ids;
    }

    private function pickLineIdentifier(array $line): ?string
    {
        return $line['offerPriceSnapshotDto']['offerPriceExternalId']
            ?? $line['offerPriceId']
            ?? $line['offerPriceExternalId']
            ?? $line['id']
            ?? null;
    }

    private function buildEcoTaxLinesPayload(array $lines): array
    {
        $payload = [];

        foreach ($lines as $line) {
            $ecoTaxValue = $this->extractProductEcoTaxValue($line);

            if ($ecoTaxValue === null || $ecoTaxValue === '') {
                continue;
            }

            $lineId = $line['id'] ?? null;
            if ($lineId === null) {
                continue;
            }

            $payload[] = [
                'logisticOrderLineId' => (string) $lineId,
                'customFieldValues' => [
                    [
                        'customFieldId' => DjustCustomField::ORDER_LINE_ECO_TAX->value,
                        'customFieldValue' => $ecoTaxValue,
                    ],
                ],
            ];
        }

        return $payload;
    }

    private function extractProductEcoTaxValue(array $line): ?string
    {
        $productAttributes = $line['orderLogisticLineProductDto']['productAttributeValues'] ?? [];

        foreach ($productAttributes as $attr) {
            $attrExternalId = $attr['attributeExternalId'] ?? null;

            if ($attrExternalId === DjustCustomField::PRODUCT_ECOTAXE->value) {
                $attrValue = $attr['attributeValue'] ?? null;

                return $attrValue !== null ? (string) $attrValue : null;
            }
        }

        return null;
    }

    private function updateLogisticOrderLinesEcoTax(string $logisticOrderId, array $linesPayload): void
    {
        $endpoint = \sprintf(DjustApiEndpoint::SHOP_LOGISTIC_ORDER_LINES->value, $logisticOrderId);

        try {
            $this->djustHttpClient->patch($endpoint, $linesPayload, $this->storeViewHeadersBuilder->build());
            $this->logger->info('[DjustCart] Mise à jour éco-participation des lignes OK.', [
                'logisticOrderId' => $logisticOrderId,
                'linesCount' => \count($linesPayload),
            ]);
        } catch (\Exception $e) {
            $this->logger->error('[DjustCart] Mise à jour éco-participation des lignes échouée — bloquant.', [
                'logisticOrderId' => $logisticOrderId,
                'error' => $e->getMessage(),
            ]);
            throw new BadRequestHttpException(
                \sprintf('Impossible de mettre à jour l\'éco-participation pour le logistic order %s.', $logisticOrderId),
                $e,
            );
        }
    }
}
