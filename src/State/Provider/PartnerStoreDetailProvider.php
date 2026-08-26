<?php

declare(strict_types=1);

namespace App\State\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Dto\StoreDetailDto;
use App\Entity\PartnerStore;
use App\Helper\Formatter\PhoneFormatter;
use App\Repository\PartnerStoreRepository;
use App\Service\Account\CurrentAccountProvider;
use App\Service\Djust\DjustSellerService;
use Doctrine\ORM\EntityNotFoundException;
use Psr\Log\LoggerInterface;

readonly class PartnerStoreDetailProvider implements ProviderInterface
{
    public function __construct(
        private PartnerStoreRepository $partnerStoreRepository,
        private PhoneFormatter $phoneFormatter,
        private LoggerInterface $logger,
        private DjustSellerService $djustSellerService,
        private CurrentAccountProvider $currentAccountProvider,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): StoreDetailDto
    {
        $storeId = $uriVariables['id'] ?? null;
        if (!$storeId) {
            throw new EntityNotFoundException('Store ID is required');
        }

        $store = $this->partnerStoreRepository->find($storeId);
        if (!$store instanceof PartnerStore) {
            throw new EntityNotFoundException('Store not found');
        }

        $partner = $store->getPartner();
        $partnerId = $partner ? (string) $partner->getId() : null;

        $accordLogos = $this->buildAccordLogos($store);

        $logo = null;
        if (!empty($accordLogos) && !empty($accordLogos[0]['logo'] ?? null)) {
            $logo = $accordLogos[0]['logo'];
        } elseif ($partnerId) {
            try {
                $customerAccountId = $this->currentAccountProvider->getAccount()?->getDjustCustomerAccountId();
                $logo = $this->djustSellerService->getSellerLogo($partnerId, $customerAccountId);
            } catch (\Throwable $e) {
                $this->logger->warning('Impossible de récupérer le logo partenaire (fallback)', [
                    'djustId' => $partnerId,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return new StoreDetailDto(
            id: (string) $store->getId(),
            name: $store->getName(),
            address: $store->getAddress(),
            phone: $this->phoneFormatter->format($store->getPhone()),
            latitude: $store->getLatitude(),
            longitude: $store->getLongitude(),
            djustId: $partnerId,
            partnerName: $partner?->getName(),
            logo: $logo,
            accordLogos: $accordLogos,
        );
    }

    private function buildAccordLogos(PartnerStore $store): array
    {
        $partner = $store->getPartner();
        if (!$partner) {
            return [];
        }

        $accords = \array_filter(
            $partner->getAccords()->toArray(),
            static fn ($accord) => $accord->getStores()->contains($store)
        );

        return \array_values(\array_filter(\array_map(static function ($accord) {
            $logo = $accord->getLogo();
            if (!empty($logo) && \filter_var($logo, \FILTER_VALIDATE_URL)) {
                return [
                    'logo' => $logo,
                    'name' => $accord->getName(),
                    'id' => (string) $accord->getId(),
                ];
            }

            return null;
        }, $accords)));
    }
}
