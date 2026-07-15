<?php

declare(strict_types=1);

namespace App\Service\Djust;

use App\Context\ChannelContext;
use App\Dto\Djust\DjustSearchParams;
use App\Enum\Djust\DjustApiEndpoint;
use App\Enum\Djust\DjustCustomField;
use App\Enum\Djust\DjustDefaults;
use App\Exception\UserAccountException;
use App\Service\AccordCadre\AccordCadreService;
use App\Service\Account\CurrentAccountProvider;
use App\Service\Djust\Search\DjustSearchService;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class DjustSellerService
{
    public const string SELLERS_CACHE_KEY = 'djust_sellers';
    private const string TARIF_MAP_CACHE_KEY = 'djust_seller_tarif_map';
    private const string ADHERENT_SELLERS_CACHE_KEY = 'djust_adherent_sellers';
    private const int CACHE_TTL_SECONDS = 300;

    private const array SELLER_DEFAULT_ATTRIBUTES = [
        'PRODUCT_TYPE|ACCORD_CADRE',
        'PRODUCT_TYPE|NOT_SELLABLE',
        'PRODUCT_TYPE|SELLABLE',
    ];

    public function __construct(
        private readonly CacheInterface $cache,
        private readonly DjustSearchService $djustSearchService,
        private readonly DjustHttpClientService $djustHttpClient,
        private readonly AccordCadreService $accordCadreService,
        private readonly ChannelContext $channelContext,
        private readonly CurrentAccountProvider $currentAccountProvider,
    ) {
    }

    public function getValidSellers(?string $customerAccountId = null, ?DjustSearchParams $params = null): array
    {
        $allSellers = $this->getAllSellers($customerAccountId) ?? [];
        $sellerTarifIdMap = $this->getAdherentSellerTarifIdMap($params);
        $storyblokTarifIds = $this->accordCadreService->getTarifIds();

        $validSellerIds = [];
        foreach ($sellerTarifIdMap as $sellerId => $tarifIds) {
            foreach ($tarifIds as $tarifId) {
                if (isset($storyblokTarifIds[$tarifId])) {
                    $validSellerIds[$sellerId] = true;
                    break;
                }
            }
        }

        return \array_values(\array_filter(
            $allSellers,
            static fn (array $seller): bool =>
                \strtoupper((string) ($seller['supplierStatus'] ?? '')) === 'ACTIVE'
                && isset($validSellerIds[$seller['id'] ?? ''])
        ));
    }

    public function getSeller(string $sellerId, ?string $customerAccountId = null): ?array
    {
        $sellersData = $this->getAllSellers($customerAccountId);

        if (empty($sellersData)) {
            return null;
        }

        return \array_find($sellersData, fn ($seller) => $seller['id'] === $sellerId || $seller['externalId'] === $sellerId);
    }

    public function getAllSellers(?string $customerAccountId = null): ?array
    {
        $cacheKey = $this->buildCachePrefix(self::SELLERS_CACHE_KEY, $customerAccountId);

        return $this->cache->get($cacheKey, function (ItemInterface $item): ?array {
            $item->expiresAfter(self::CACHE_TTL_SECONDS);
            $sellers = [];

            $page = 0;
            do {
                $data = $this->djustHttpClient->get(DjustApiEndpoint::SHOP_SUPPLIERS->value, [
                    'page' => $page,
                    'size' => 500,
                ]);

                ++$page;
                $sellers = \array_merge($sellers, $data);
            } while (!empty($data));

            return $sellers;
        });
    }

    public function getAllAdherentSellers(?DjustSearchParams $params = null): array
    {
        $base = $params ?? new DjustSearchParams();
        $cacheKey = $this->buildCachePrefix(self::ADHERENT_SELLERS_CACHE_KEY).'_'.md5(\serialize([
                $base->query,
                $base->categoryIds,
                $base->suppliers,
            ]));

        return $this->cache->get($cacheKey, function (ItemInterface $item) use ($base): array {
            $item->expiresAfter(self::CACHE_TTL_SECONDS);
            $search = $this->djustSearchService->search($base->withAttributes(self::SELLER_DEFAULT_ATTRIBUTES));

            return $search['facets']['suppliers'] ?? [];
        });
    }

    public function getAdherentSellerTarifIdMap(?DjustSearchParams $params = null): array
    {
        $base = $params ?? new DjustSearchParams();
        $cacheKey = $this->buildCachePrefix(self::TARIF_MAP_CACHE_KEY).'_'.md5(\serialize([
                $base->query,
                $base->categoryIds,
                $base->suppliers,
                $base->attributes,
            ]));

        return $this->cache->get($cacheKey, function (ItemInterface $item) use ($base): array {
            $item->expiresAfter(self::CACHE_TTL_SECONDS);

            $fatParams = new DjustSearchParams(
                query: $base->query,
                locale: $base->locale,
                page: '0',
                size: DjustDefaults::SEARCH_PER_PAGE_ACCORD_CADRE->value,
                categoryIds: $base->categoryIds,
                suppliers: $base->suppliers,
                attributes: ['PRODUCT_TYPE|ACCORD_CADRE'],
                productTags: $base->productTags,
                aggregation: $base->aggregation,
            );

            $map = [];
            $page = 0;

            do {
                $search = $this->djustSearchService->search($fatParams->withPage((string) $page));
                $content = $search['products']['content'] ?? [];

                foreach ($content as $product) {
                    $supplierId = (string) ($product['supplier']['id'] ?? '');
                    if ($supplierId === '') {
                        continue;
                    }

                    $tarifId = $this->extractTarifIdFromRawSearchItem($product);
                    if ($tarifId !== null) {
                        if (!isset($map[$supplierId])) {
                            $map[$supplierId] = [];
                        }
                        $map[$supplierId][] = $tarifId;
                    }
                }

                $totalPages = $search['products']['totalPages'] ?? 1;
                ++$page;
            } while ($page < $totalPages);

            return $map;
        });
    }

    public function getSellerLogo(string $sellerId, ?string $customerAccountId = null): ?string
    {
        $seller = $this->getSeller($sellerId, $customerAccountId);

        return $seller['logo'] ?? null;
    }

    private function buildCachePrefix(string $key, ?string $customerAccountId = null): string
    {
        $channelCode = $this->channelContext->getChannel()->getCode();
        $accountId = $customerAccountId ?? $this->currentAccountProvider->getAccount()?->getDjustCustomerAccountId();

        if ($accountId === null) {
            throw new UserAccountException('No Djust customer account ID available.');
        }

        return $key.'_'.$channelCode.'_'.$accountId;
    }

    private function extractTarifIdFromRawSearchItem(array $item): ?string
    {
        foreach ($item['offer']['customFields'] ?? [] as $field) {
            $externalId = $field['externalId'] ?? $field['customField']['externalId'] ?? '';

            if ($externalId === DjustCustomField::OFFER_TARIF_ID->value) {
                $value = $field['value']['value'] ?? $field['value'] ?? null;

                return $value !== null ? (string) $value : null;
            }
        }

        return null;
    }
}
