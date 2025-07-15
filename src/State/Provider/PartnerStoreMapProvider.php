<?php

declare(strict_types=1);

namespace App\State\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Dto\MapStoreDataDto;
use App\Repository\PartnerRepository;
use App\Service\MapStoreBuilderService;
use App\Service\UpplerPartnerService;
use Psr\Log\LoggerInterface;

readonly class PartnerStoreMapProvider implements ProviderInterface
{
    public function __construct(
        private readonly UpplerPartnerService $upplerPartnerService,
        private readonly PartnerRepository $partnerRepository,
        private readonly LoggerInterface $logger,
        private readonly MapStoreBuilderService $mapStoreBuilderService,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): MapStoreDataDto
    {
        try {
            $categoryId = $this->extractCategoryId($context);
            $allPartnerUpplerIds = $this->getAllAuthorizedPartnerIds();

            if (empty($allPartnerUpplerIds)) {
                return $this->createEmptyResponse();
            }

            $categories = $this->getCategories($allPartnerUpplerIds);

            $finalUpplerIds = $categoryId
            ? $this->upplerPartnerService->getPartnersWithCategory($allPartnerUpplerIds, $categoryId)
            : $allPartnerUpplerIds;

            if (empty($finalUpplerIds)) {
                return new MapStoreDataDto([], $categories);
            }

            $stores = $this->mapStoreBuilderService->buildStores($finalUpplerIds);

            return new MapStoreDataDto($stores, $categories);
        } catch (\Exception $e) {
            $this->logError($e);

            return $this->createEmptyResponse();
        }
    }

    private function extractCategoryId(array $context): ?int
    {
        return isset($context['filters']['categoryId']) ? (int) $context['filters']['categoryId'] : null;
    }

    private function getAllAuthorizedPartnerIds(): array
    {
        $authorizedUpplerIds = $this->upplerPartnerService->getAuthorizedPartnerIds();
        if (empty($authorizedUpplerIds)) {
            return [];
        }

        $partnersInDb = $this->partnerRepository->findAuthorizedPartnersWithStores($authorizedUpplerIds);

        return \array_map(fn ($p) => $p->getUpplerId(), $partnersInDb);
    }

    private function getCategories(array $partnerUpplerIdsWithStores): array
    {
        try {
            $categories = $this->upplerPartnerService->getAvailableCategories($partnerUpplerIdsWithStores);

            return $this->formatCategories($categories);
        } catch (\Exception $e) {
            $this->logger->error('Erreur lors de la récupération des catégories', [
                'error' => $e->getMessage(),
            ]);

            return $this->getDefaultCategories();
        }
    }

    private function formatCategories(array $categories): array
    {
        $formattedCategories = $this->getDefaultCategories();

        foreach ($categories as $category) {
            if ($this->isTopLevelCategory($category)) {
                $formattedCategories[] = [
                    'id' => (string) $category['id'],
                    'name' => $category['name'],
                ];
            }
        }

        return $formattedCategories;
    }

    private function isTopLevelCategory(array $category): bool
    {
        return !isset($category['parentId'])
            || $category['parentId'] === 0
            || $category['parentId'] === null;
    }

    private function getDefaultCategories(): array
    {
        return [['id' => 'all', 'name' => 'Toutes les catégories']];
    }

    private function createEmptyResponse(): MapStoreDataDto
    {
        return new MapStoreDataDto([], $this->getDefaultCategories());
    }

    private function logError(\Exception $e): void
    {
        $this->logger->error('Erreur lors de la récupération des données de la map', [
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
        ]);
    }
}
