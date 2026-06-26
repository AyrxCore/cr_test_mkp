<?php

declare(strict_types=1);

namespace App\Factory;

use App\Dto\Seller;
use App\Service\Djust\DjustSellerService;
use Psr\Cache\CacheItemPoolInterface as AdapterInterface;

class SellerFactory extends AbstractFactory
{
    public const string CUSTOM_FIELD_TOS = 'SUPPLIER_CGV';
    public const string CUSTOM_FIELD_SUPPLIER_DELIVERY_INFO = 'SUPPLIER_DELIVERY_INFO';

    public function __construct(
        protected AdapterInterface $cache,
        private readonly DjustSellerService $djustSellerService,
    ) {
        parent::__construct($this->cache);
    }

    public function createAndAddToCollection(array $data, ?string $customerAccountId = null): array
    {
        $result = [];
        foreach ($data as $item) {
            $result[] = $this->create($item, $customerAccountId);
        }

        return $result;
    }

    public function create(array $data, ?string $customerAccountId = null): Seller
    {
        $sellerData = $this->djustSellerService->getSeller($data['id'], $customerAccountId);

        $seller = new Seller();
        $seller->setId($sellerData['id'] ?? null)
        ->setExternalId($sellerData['externalId'] ?? null)
        ->setName($sellerData['name'] ?? '')
        ->setTos($this->extractSupplierFieldByExternalId($sellerData['customFieldValues'], self::CUSTOM_FIELD_TOS))
        ->setDescription($sellerData['returnPolicy'] ?? '')
        ->setAvatar($sellerData['logo'] ?? null)
        ->setSupplierDeliveryInfo($this->extractSupplierFieldByExternalId($sellerData['customFieldValues'], self::CUSTOM_FIELD_SUPPLIER_DELIVERY_INFO));

        return $seller;
    }

    private function extractSupplierFieldByExternalId(?array $customFieldValues, string $externalId, $exact = true): mixed
    {
        if (!$customFieldValues) {
            return null;
        }
        $expectedExternalId = \strtolower($externalId);

        $results = $exact ? null : [];
        foreach ($customFieldValues as $cfv) {
            $fieldExternalId = \strtolower($cfv['customField']['externalId'] ?? '');

            if ($exact && $fieldExternalId === $expectedExternalId) {
                return $this->extractCustomFieldValueWrapper($cfv['value'] ?? null);
            } elseif (\str_starts_with($fieldExternalId, $expectedExternalId)) {
                $results[$fieldExternalId] = $this->extractCustomFieldValueWrapper($cfv['value'] ?? null);
            }
        }

        return $results;
    }

    private function extractCustomFieldValueWrapper(mixed $valueWrapper): mixed
    {
        if (\is_array($valueWrapper) && isset($valueWrapper['value'])) {
            return $valueWrapper['value'];
        }

        return $valueWrapper;
    }
}
