<?php

declare(strict_types=1);

namespace App\Service;

use App\Helper\UpplerHelper;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class UpplerPartnerService
{
    public const CACHE_ONE_DAY = 86400;
    private const ALL_LOGOS_CACHE_KEY = 'all_partner_logos';

    public function __construct(
        private readonly UpplerProductService $upplerProductService,
        private readonly UpplerSellerService $upplerSellerService,
        private readonly LoggerInterface $apiLogger,
        private readonly CacheInterface $cache,
    ) {
    }

    public function getAuthorizedPartnerIds(): array
    {
        try {
            $companies = $this->upplerProductService->findAllSellers();

            return \array_map(
                fn ($company) => $company['id'],
                \array_values($companies)
            );
        } catch (\Exception $e) {
            $this->apiLogger->error('Erreur lors de la récupération des partenaires autorisés', [
                'error' => $e->getMessage(),
                'exception' => $e::class,
            ]);

            throw new ServiceUnavailableHttpException(null, 'Impossible de récupérer les partenaires autorisés', $e);
        }
    }

    public function getPartnerLogoFromCacheOrAdmin(int $upplerId): ?string
    {
        if ($upplerId <= 0) {
            return null;
        }

        try {
            $allLogos = $this->getAllPartnerLogos();

            if (isset($allLogos[$upplerId])) {
                return $allLogos[$upplerId];
            }

            $seller = $this->upplerSellerService->getSeller($upplerId);

            return $seller['logo'] ?? ($seller['avatar_url'] ?? null);
        } catch (\Throwable $e) {
            $this->apiLogger->error('Erreur lors de la récupération du logo partenaire', [
                'upplerId' => $upplerId,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Récupère tous les logos partenaires depuis le cache ou l'API.
     */
    public function getAllPartnerLogos(): array
    {
        return $this->cache->get(self::ALL_LOGOS_CACHE_KEY, function (ItemInterface $item) {
            $item->expiresAfter(self::CACHE_ONE_DAY);
            try {
                $allSellers = UpplerHelper::getAllPaginatedResults(
                    fn ($perPage, $page) => $this->upplerSellerService->getSellersAdmin($perPage, $page)
                );

                $logos = [];
                foreach ($allSellers as $seller) {
                    $id = (int) ($seller['id'] ?? 0);
                    if ($id <= 0) {
                        continue;
                    }

                    $logo = $seller['logo'] ?? ($seller['avatar_url'] ?? null);
                    if (!empty($logo)) {
                        $logos[$id] = $logo;
                    }
                }

                $this->apiLogger->info('Cache global logos créé', [
                    'count' => \count($logos),
                ]);

                return $logos;
            } catch (\Throwable $e) {
                $this->apiLogger->error('Erreur lors de la création du cache global logos', [
                    'error' => $e->getMessage(),
                ]);

                return [];
            }
        });
    }

    public function getAvailableCategories(array $authorizedUpplerIds): array
    {
        if (empty($authorizedUpplerIds)) {
            return [];
        }

        try {
            $result = $this->upplerProductService->findProducts(
                options: [
                    'companies' => $authorizedUpplerIds,
                ],
                expands: [],
                page: 1,
                perPage: 1
            );

            // Extraire les catégories des filtres
            return $result['filters']['category'] ?? [];
        } catch (BadRequestHttpException $e) {
            $this->apiLogger->error('Erreur lors de la récupération des catégories', [
                'error' => $e->getMessage(),
            ]);

            throw new ServiceUnavailableHttpException(null, 'Impossible de récupérer les catégories', $e);
        }
    }

    public function getPartnersWithCategory(array $upplerIds, int $categoryId): array
    {
        if (empty($upplerIds)) {
            return [];
        }

        try {
            $result = $this->upplerProductService->findProducts(
                options: [
                    'companies' => $upplerIds,
                    'categories' => [$categoryId],
                ],
                expands: [],
                page: 1,
                perPage: 1
            );

            $companiesInFilters = $result['filters']['company'] ?? [];

            $partnerIds = [];
            foreach ($companiesInFilters as $company) {
                if (isset($company['id']) && \in_array($company['id'], $upplerIds, true)) {
                    $partnerIds[] = $company['id'];
                }
            }

            return $partnerIds;
        } catch (BadRequestHttpException $e) {
            $this->apiLogger->error('Erreur lors de la récupération des partenaires par catégorie', [
                'error' => $e->getMessage(),
                'categoryId' => $categoryId,
            ]);
        } catch (\Exception $e) {
            $this->apiLogger->error('Erreur lors de la récupération des partenaires par catégorie', [
                'error' => $e->getMessage(),
                'categoryId' => $categoryId,
            ]);
        }

        return [];
    }
}
