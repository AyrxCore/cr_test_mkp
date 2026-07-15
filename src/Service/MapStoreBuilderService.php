<?php

declare(strict_types=1);

namespace App\Service;

use App\Dto\MapStoreDto;
use App\Entity\Partner;
use App\Entity\PartnerStore;

readonly class MapStoreBuilderService
{
    public function __construct()
    {
    }

    public function buildStores(array $partners): array
    {
        $stores = [];
        foreach ($partners as $partner) {
            \array_push($stores, ...$this->createStoresForPartner($partner));
        }

        return $stores;
    }

    private function createStoresForPartner(Partner $partner): array
    {
        return \array_map(
            fn (PartnerStore $store) => $this->createMapStoreDto($store),
            $partner->getPartnerStores()->toArray()
        );
    }

    private function createMapStoreDto(PartnerStore $store): MapStoreDto
    {
        return new MapStoreDto(
            id: (string) $store->getId(),
            latitude: $store->getLatitude(),
            longitude: $store->getLongitude(),
        );
    }
}
