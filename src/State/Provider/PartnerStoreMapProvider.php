<?php

declare(strict_types=1);

namespace App\State\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Dto\MapStoreDataDto;
use App\Dto\MapStoreDto;
use App\Helper\Formatter\PhoneFormatter;
use App\Repository\PartnerRepository;
use App\Service\UpplerPartnerService;
use Psr\Log\LoggerInterface;

readonly class PartnerStoreMapProvider implements ProviderInterface
{
    public function __construct(
        private readonly UpplerPartnerService $upplerPartnerService,
        private readonly PartnerRepository $partnerRepository,
        private readonly LoggerInterface $logger,
        private readonly PhoneFormatter $phoneFormatter,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): MapStoreDataDto
    {
        try {
            $categoryId = isset($context['filters']['categoryId']) ? (int) $context['filters']['categoryId'] : null;

            // 1. Récupérer les partenaires autorisés depuis Uppler
            $authorizedUpplerIds = $this->upplerPartnerService->getAuthorizedPartnerIds();

            if (empty($authorizedUpplerIds)) {
                return new MapStoreDataDto([], []);
            }

            // 2. Récupérer les partenaires en base qui ont des stores
            $allPartnersInDb = $this->partnerRepository->findAuthorizedPartnersWithStores($authorizedUpplerIds);
            $allPartnerUpplerIdsInDb = \array_map(fn ($p) => $p->getUpplerId(), $allPartnersInDb);

            if (empty($allPartnerUpplerIdsInDb)) {
                return new MapStoreDataDto([], []);
            }

            // 3. Récupérer les catégories disponibles et les filtrer
            $categories = $this->getCategories($allPartnerUpplerIdsInDb);

            // 4. Déterminer les partenaires finaux pour les stores (avec ou sans filtre catégorie)
            if ($categoryId) {
                $partnersWithCategory = $this->upplerPartnerService->getPartnersWithCategory($allPartnerUpplerIdsInDb, $categoryId);

                if (empty($partnersWithCategory)) {
                    return new MapStoreDataDto([], $categories);
                }

                $finalUpplerIds = \array_intersect($partnersWithCategory, $allPartnerUpplerIdsInDb);
            } else {
                $finalUpplerIds = $allPartnerUpplerIdsInDb;
            }

            // 5. Récupérer les partenaires de la base de données avec leurs stores (filtrés si nécessaire)
            $partners = $this->partnerRepository->findAuthorizedPartnersWithStores($finalUpplerIds);

            // 6. Récupérer les données Uppler (logos, etc.)
            $upplerData = $this->upplerPartnerService->getPartnersData($finalUpplerIds);

            // Log pour diagnostiquer les logos
            $logosCount = \count(\array_filter($upplerData, fn ($p) => !empty($p['logo'] ?? null)));
            $this->logger->info('Données Uppler récupérées pour la map', [
                'partnersCount' => \count($upplerData),
                'logosCount' => $logosCount,
                'samplePartner' => !empty($upplerData) ? \array_slice($upplerData, 0, 1, true) : null,
            ]);

            // 7. Combiner les données et créer les DTOs pour les stores
            $stores = [];
            foreach ($partners as $partner) {
                $upplerInfo = $upplerData[$partner->getUpplerId()] ?? null;

                foreach ($partner->getPartnerStores() as $store) {
                    $stores[] = new MapStoreDto(
                        id: (string) $store->getId(),
                        name: $store->getName(),
                        address: $store->getAddress(),
                        phone: $this->phoneFormatter->format($store->getPhone()),
                        latitude: $store->getLatitude(),
                        longitude: $store->getLongitude(),
                        upplerId: $partner->getUpplerId(),
                        partnerName: $partner->getName(),
                        partnerLogo: $upplerInfo['logo'] ?? null
                    );
                }
            }

            return new MapStoreDataDto($stores, $categories);
        } catch (\Exception $e) {
            $this->logger->error('Erreur lors de la récupération des données de la map', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            // Retourner des données vides en cas d'erreur
            return new MapStoreDataDto([], []);
        }
    }

    private function getCategories(array $partnerUpplerIdsWithStores): array
    {
        try {
            $categories = $this->upplerPartnerService->getAvailableCategories($partnerUpplerIdsWithStores);

            $formattedCategories = [
                ['id' => 'all', 'name' => 'Toutes les catégories'],
            ];

            foreach ($categories as $category) {
                if (!isset($category['parentId']) || $category['parentId'] === 0 || $category['parentId'] === null) {
                    $formattedCategories[] = [
                        'id' => (string) $category['id'],
                        'name' => $category['name'],
                    ];
                }
            }

            return $formattedCategories;
        } catch (\Exception $e) {
            $this->logger->error('Erreur lors de la récupération des catégories', [
                'error' => $e->getMessage(),
            ]);

            return [['id' => 'all', 'name' => 'Toutes les catégories']];
        }
    }
}
