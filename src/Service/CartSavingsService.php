<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Account;
use App\Entity\CartSavings;
use App\Repository\AccountRepository;
use App\Repository\CartSavingsRepository;
use App\Service\Djust\DjustDataExtractor;
use App\Service\Djust\DjustOrderService;
use App\Service\Djust\DjustProductService;
use App\Service\Djust\ProductFdpSyncService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class CartSavingsService
{
    /** @var array<string, null|float> */
    private array $catalogPriceCache = [];

    public function __construct(
        private readonly CartSavingsRepository $cartSavingsRepository,
        private readonly AccountRepository $accountRepository,
        private readonly DjustOrderService $djustOrderService,
        private readonly DjustProductService $djustProductService,
        private readonly DjustDataExtractor $djustDataExtractor,
        private readonly EntityManagerInterface $entityManager,
        private readonly LoggerInterface $logger,
    ) {
    }

    /**
     * Crée CartSavings en récupérant la commande la plus récente de l'acheteur (API buyer, session requise).
     * À appeler juste après un paiement confirmé, pendant que la session est encore active.
     */
    public function createAfterPayment(string $cartReference, ?Account $account): void
    {
        if ($account === null) {
            $this->logger->warning('CartSavings not created: no account in session.', [
                'cartReference' => $cartReference,
            ]);

            return;
        }

        $count = 0;

        try {
            $count = $this->createFromMostRecentBuyerOrder($account);
        } catch (\Throwable $throwable) {
            $this->logger->warning('CartSavings: buyer order fetch failed.', [
                'cartReference' => $cartReference,
                'accountId' => $account->getId()?->toRfc4122(),
                'error' => $throwable->getMessage(),
            ]);
        }

        if ($count > 0) {
            $this->logger->info('CartSavings created after payment.', [
                'cartReference' => $cartReference,
                'count' => $count,
            ]);

            return;
        }

        $this->logger->warning('CartSavings not created after payment: no order found.', [
            'cartReference' => $cartReference,
        ]);
    }

    public function createFromMostRecentBuyerOrder(Account $account): int
    {
        $order = $this->djustOrderService->getMostRecentBuyerOrder();

        if ($order === null) {
            $this->logger->warning('Unable to create CartSavings: no recent order found for buyer.', [
                'accountId' => $account->getId()?->toRfc4122(),
            ]);

            return 0;
        }

        $this->logger->info('Creating CartSavings from most recent buyer order.', [
            'orderId' => $order['id'] ?? null,
            'reference' => $order['reference'] ?? null,
            'accountId' => $account->getId()?->toRfc4122(),
        ]);

        return $this->createFromDjustOrder($order, $account);
    }

    public function createFromDjustOrder(array $djustOrder, Account $account): int
    {
        $orderId = (string) ($djustOrder['id'] ?? '');
        if ($orderId === '') {
            $this->logger->warning('Unable to create CartSavings: missing Djust order id.', [
                'reference' => $djustOrder['reference'] ?? null,
                'accountId' => $account->getId()?->toRfc4122(),
            ]);

            return 0;
        }

        // Recharge l'Account (issu de la session) dans le contexte Doctrine courant pour éviter "detached entity".
        $accountId = $account->getId();
        if ($accountId !== null) {
            $managedAccount = $this->accountRepository->find($accountId);
            if ($managedAccount !== null) {
                $account = $managedAccount;
            }
        }

        $cartId = (string) ($djustOrder['reference'] ?? $orderId);
        $orderLogistics = $djustOrder['orderLogistics'] ?? [];
        $updatedCount = 0;

        foreach ($orderLogistics as $orderLogistic) {
            $sellerId = $this->extractSellerId($orderLogistic);
            $metrics = $this->calculateMetrics($orderLogistic);

            $cartSaving = $this->cartSavingsRepository->findOneBy([
                'account' => $account,
                'orderId' => $orderId,
                'sellerId' => $sellerId,
            ]) ?? new CartSavings();

            $cartSaving->setAccount($account);
            $cartSaving->setCartId($cartId);
            $cartSaving->setOrderId($orderId);
            $cartSaving->setSellerId($sellerId);
            $cartSaving->setAmount($metrics['amount']);
            $cartSaving->setOrderTotal($metrics['orderTotal']);
            $cartSaving->setItemsTotalBeforeSavings($metrics['itemsTotalBeforeSavings']);
            $cartSaving->setItemsTotal($metrics['itemsTotal']);
            $cartSaving->setOrderState($this->extractOrderStatus($djustOrder, $orderLogistic));

            $this->cartSavingsRepository->save($cartSaving);
            ++$updatedCount;
        }

        if ($updatedCount > 0) {
            $this->entityManager->flush();
        }

        return $updatedCount;
    }

    private function extractSellerId(array $orderLogistic): int
    {
        $rawSellerId = $orderLogistic['supplierSnapshot']['id'] ?? null;

        if ($rawSellerId === null) {
            return 0;
        }

        if (\is_int($rawSellerId)) {
            return $rawSellerId;
        }

        if (\is_string($rawSellerId) && \preg_match('/\d+/', $rawSellerId, $matches) === 1) {
            return (int) $matches[0];
        }

        return 0;
    }

    /**
     * @return array{amount:int, orderTotal:int, itemsTotalBeforeSavings:int, itemsTotal:int}
     */
    private function calculateMetrics(array $orderLogistic): array
    {
        $itemsTotal = 0;
        $itemsTotalBeforeSavings = 0;

        foreach ($orderLogistic['lines'] ?? [] as $line) {
            if ($this->isFdpLine($line)) {
                continue;
            }

            $quantity = (int) ($line['quantity'] ?? 0);
            $linePrices = $line['orderLogisticLinePriceDto'] ?? [];

            // totalItemPrice exclut la livraison ; fallback totalPriceWithoutTaxes pour compatibilité Uppler.
            $paidTotal = (float) ($linePrices['totalItemPrice']
                ?? $linePrices['totalPriceWithoutTaxes']
                ?? 0.0);

            $referenceUnitPrice = $this->extractReferenceUnitPrice($line, $linePrices);

            $itemsTotal += $this->toCents($paidTotal);
            $itemsTotalBeforeSavings += $this->toCents($referenceUnitPrice * $quantity);
        }

        $orderTotal = $itemsTotal;
        $orderLogisticPrices = $orderLogistic['orderLogisticPrices'] ?? [];
        if (isset($orderLogisticPrices['totalPriceWithoutTax'])) {
            $orderTotal = $this->toCents((float) $orderLogisticPrices['totalPriceWithoutTax']);
        }

        return [
            'amount' => $itemsTotalBeforeSavings - $itemsTotal,
            'orderTotal' => $orderTotal,
            'itemsTotalBeforeSavings' => $itemsTotalBeforeSavings,
            'itemsTotal' => $itemsTotal,
        ];
    }

    private function extractReferenceUnitPrice(array $line, array $linePrices): float
    {
        $publicPrice = $this->extractPublicPrice($line);
        if ($publicPrice !== null) {
            return $publicPrice;
        }

        $catalogPrice = $this->fetchReferenceUnitPriceFromCatalog($line);
        if ($catalogPrice !== null) {
            return $catalogPrice;
        }

        // Fallback snapshot : économies = 0 si prix de référence = prix payé.
        $offerPriceSnapshot = $line['offerPriceSnapshotDto'] ?? [];

        return (float) ($offerPriceSnapshot['productPriceWithoutTaxes']
            ?? $offerPriceSnapshot['priceWithoutTaxes']
            ?? $linePrices['itemPriceWithoutTaxes']
            ?? 0.0);
    }

    private function extractPublicPrice(array $line): ?float
    {
        foreach ($line['offerCustomFieldSnapshotDtos'] ?? [] as $customField) {
            if (($customField['offerCustomFieldSnapshotDto']['externalId'] ?? '') === 'OFFER_PUBLIC_PRICE') {
                $value = $customField['value'] ?? null;
                if ($value !== null && $value !== '') {
                    return (float) $value;
                }
            }
        }

        foreach ($line['offerInventorySnapshotDto']['customFieldValueSnapshots'] ?? [] as $snapshot) {
            if (($snapshot['customFieldSnapshotDto']['externalId'] ?? '') === 'OFFER_PUBLIC_PRICE') {
                $value = $snapshot['typedValue'] ?? $snapshot['value'] ?? null;
                if ($value !== null && $value !== '') {
                    return (float) $value;
                }
            }
        }

        return null;
    }

    /**
     * Récupère le prix de référence (priceReference) depuis l'API offer-prices Djust.
     * Requiert un contexte web avec session acheteur active.
     */
    private function fetchReferenceUnitPriceFromCatalog(array $line): ?float
    {
        $offerInventoryExternalId = $line['offerInventorySnapshotDto']['offerInventoryExternalId'] ?? null;
        $offerExternalId = $line['offerPriceSnapshotDto']['offerPriceExternalId'] ?? null;

        $productDto = $line['orderLogisticLineProductDto'] ?? [];
        $productExternalId = $productDto['djustProductUuid'] ?? $productDto['externalId'] ?? null;

        $cacheKey = ($offerInventoryExternalId ?? $productExternalId ?? 'unknown').'|'.($offerExternalId ?? '');

        if (\array_key_exists($cacheKey, $this->catalogPriceCache)) {
            return $this->catalogPriceCache[$cacheKey];
        }

        try {
            if ($offerInventoryExternalId !== null) {
                // Réponse paginée : {content: [{priceRanges: [...]}, ...]}
                $response = $this->djustProductService->getOffersByOfferInventory($offerInventoryExternalId);
                $content = $response['content'] ?? [];
                $priceData = $this->djustDataExtractor->extractSingleOfferPrice(['offerPrices' => $content]);
            } elseif ($productExternalId !== null) {
                $offers = $this->djustProductService->getProductOffers($productExternalId);
                $priceData = null;
                foreach ($offers as $offer) {
                    if ($offerExternalId !== null && ($offer['externalId'] ?? null) !== $offerExternalId) {
                        continue;
                    }
                    $priceData = $this->djustDataExtractor->extractSingleOfferPrice($offer);
                    break;
                }
            } else {
                $this->catalogPriceCache[$cacheKey] = null;

                return null;
            }

            $priceReference = $priceData['priceReference'] ?? null;

            if ($priceReference !== null && (float) $priceReference > 0.0) {
                $this->catalogPriceCache[$cacheKey] = (float) $priceReference;

                return (float) $priceReference;
            }
        } catch (\Throwable $e) {
            $this->logger->warning('CartSavings: unable to fetch catalog price from Djust API.', [
                'offerInventoryExternalId' => $offerInventoryExternalId,
                'productExternalId' => $productExternalId,
                'error' => $e->getMessage(),
            ]);
        }

        $this->catalogPriceCache[$cacheKey] = null;

        return null;
    }

    private function extractOrderStatus(array $djustOrder, array $orderLogistic): ?string
    {
        return $orderLogistic['status']
            ?? $djustOrder['status']
            ?? null;
    }

    private function isFdpLine(array $line): bool
    {
        $externalId = $line['orderLogisticLineProductDto']['externalId'] ?? '';

        return \str_starts_with((string) $externalId, ProductFdpSyncService::EXTERNAL_ID_PREFIX);
    }

    private function toCents(float $amount): int
    {
        return (int) \round($amount * 100);
    }
}
