<?php

declare(strict_types=1);

namespace App\Service;

use App\Dto\MapStoreDto;
use App\Entity\Partner;
use App\Entity\PartnerStore;
use App\Repository\PartnerRepository;
use Psr\Log\LoggerInterface;

readonly class MapStoreBuilderService
{
    public function __construct(
        private UpplerPartnerService $upplerPartnerService,
        private PartnerRepository $partnerRepository,
        private LoggerInterface $logger,
    ) {
    }

    public function buildStores(array $finalUpplerIds): array
    {
        $partners = $this->partnerRepository->findAuthorizedPartnersWithStores($finalUpplerIds);

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
