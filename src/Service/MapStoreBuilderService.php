<?php

declare(strict_types=1);

namespace App\Service;

use App\Dto\MapStoreDto;
use App\Entity\Partner;
use App\Entity\PartnerStore;
use App\Helper\Formatter\PhoneFormatter;
use App\Repository\PartnerRepository;
use Psr\Log\LoggerInterface;

readonly class MapStoreBuilderService
{
    public function __construct(
        private UpplerPartnerService $upplerPartnerService,
        private PartnerRepository $partnerRepository,
        private LoggerInterface $logger,
        private PhoneFormatter $phoneFormatter,
    ) {
    }

    public function buildStores(array $finalUpplerIds): array
    {
        $partners = $this->partnerRepository->findAuthorizedPartnersWithStoresAndAccords($finalUpplerIds);
        $upplerData = $this->fetchAndLogUpplerData($finalUpplerIds);

        $stores = [];
        foreach ($partners as $partner) {
            $upplerInfo = $upplerData[$partner->getUpplerId()] ?? null;
            array_push($stores, ...$this->createStoresForPartner($partner, $upplerInfo));
        }

        return $stores;
    }

    private function fetchAndLogUpplerData(array $finalUpplerIds): array
    {
        $upplerData = $this->upplerPartnerService->getPartnersData($finalUpplerIds);

        $logosCount = \count(\array_filter($upplerData, fn ($p) => !empty($p['logo'] ?? null)));
        $this->logger->info('Données Uppler récupérées pour la map', [
            'partnersCount' => \count($upplerData),
            'logosCount' => $logosCount,
            'samplePartner' => !empty($upplerData) ? \array_slice($upplerData, 0, 1, true) : null,
        ]);

        return $upplerData;
    }

    private function createStoresForPartner(Partner $partner, ?array $upplerInfo): array
    {
        return \array_map(
            fn (PartnerStore $store) => $this->createMapStoreDto($partner, $store, $upplerInfo),
            $partner->getPartnerStores()->toArray()
        );
    }

    private function createMapStoreDto(Partner $partner, PartnerStore $store, ?array $upplerInfo): MapStoreDto
    {
        return new MapStoreDto(
            id: (string) $store->getId(),
            name: $store->getName(),
            address: $store->getAddress(),
            phone: $this->phoneFormatter->format($store->getPhone()),
            latitude: $store->getLatitude(),
            longitude: $store->getLongitude(),
            upplerId: $partner->getUpplerId(),
            partnerName: $partner->getName(),
            logo: $upplerInfo['logo'] ?? null,
            accordLogos: $this->buildAccordLogos($partner, $store, $upplerInfo)
        );
    }

    private function buildAccordLogos(Partner $partner, PartnerStore $store, ?array $upplerInfo): array
    {
        $accordsForStore = $this->getAccordsForStore($partner, $store);

        return \array_values(\array_filter(\array_map(
            fn ($accord) => $this->createAccordLogoData($accord, $upplerInfo),
            $accordsForStore
        )));
    }

    private function getAccordsForStore(Partner $partner, PartnerStore $store): array
    {
        return \array_filter(
            $partner->getAccords()->toArray(),
            fn ($accord) => $accord->getStores()->contains($store)
        );
    }

    private function createAccordLogoData($accord, ?array $upplerInfo): ?array
    {
        $logo = $this->resolveAccordLogo($accord, $upplerInfo);

        return $logo ? [
            'logo' => $logo,
            'name' => $accord->getName(),
            'id' => (string) $accord->getId(),
        ] : null;
    }

    private function resolveAccordLogo($accord, ?array $upplerInfo): ?string
    {
        $accordLogo = $accord->getLogo();

        if (!empty($accordLogo) && \filter_var($accordLogo, \FILTER_VALIDATE_URL)) {
            return $accordLogo;
        }

        return $upplerInfo['logo'] ?? null;
    }
}
