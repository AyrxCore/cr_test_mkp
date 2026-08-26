<?php

declare(strict_types=1);

namespace App\State\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Dto\MapStoreDataDto;
use App\Factory\CategoryFactory;
use App\Mapper\DjustSearchParamsMapper;
use App\Repository\PartnerRepository;
use App\Service\Djust\DjustCategoryService;
use App\Service\Djust\Search\DjustSearchService;
use App\Service\MapStoreBuilderService;
use Psr\Log\LoggerInterface;

readonly class PartnerStoreMapProvider implements ProviderInterface
{
    public function __construct(
        private readonly CategoryFactory $categoryFactory,
        private readonly DjustCategoryService $djustCategoryService,
        private readonly DjustSearchParamsMapper $djustSearchParamsMapper,
        private readonly DjustSearchService $djustSearchService,
        private readonly LoggerInterface $logger,
        private readonly MapStoreBuilderService $mapStoreBuilderService,
        private readonly PartnerRepository $partnerRepository,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): MapStoreDataDto
    {
        try {
            $partners = $this->getPartners($context);

            if (empty($partners)) {
                return $this->createEmptyResponse();
            }

            $partnersDjustIds = \array_map(static fn ($p) => (string) $p->getId(), $partners);

            $categories = $this->getCategories($partnersDjustIds);

            if (empty($partnersDjustIds)) {
                return new MapStoreDataDto([], $categories);
            }

            $stores = $this->mapStoreBuilderService->buildStores($partners);

            return new MapStoreDataDto($stores, $categories);
        } catch (\Exception $e) {
            $this->logError($e);

            return $this->createEmptyResponse();
        }
    }

    private function getPartners(array $context): array
    {
        $search = $this->djustSearchService->search(
            $this->djustSearchParamsMapper->fromContext($context),
        );
        $authorizedDjustIds = \array_map(static fn ($seller) => $seller['externalId'], $search['facets']['suppliers'] ?? []);

        return $this->partnerRepository->findByDjustIds($authorizedDjustIds);
    }

    private function getCategories(array $partnerDjustIdsWithStores): array
    {
        try {
            $categories = $this->djustCategoryService->getAvailableCategories(
                $this->djustSearchParamsMapper->fromContext(['filters' => ['sellers' => $partnerDjustIdsWithStores]]),
            );

            return $this->categoryFactory->createAndAddToCollection([...$this->getDefaultCategories(), ...$categories]);
        } catch (\Exception $e) {
            $this->logger->error('Erreur lors de la récupération des catégories', [
                'error' => $e->getMessage(),
            ]);

            return $this->getDefaultCategories();
        }
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
