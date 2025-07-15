<?php

declare(strict_types=1);

namespace App\Service;

use App\Helper\UpplerHelper;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;

class UpplerPartnerService
{
    public function __construct(
        private readonly UpplerProductService $upplerProductService,
        private readonly UpplerSellerService $upplerSellerService,
        private readonly LoggerInterface $apiLogger,
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

    public function getPartnersData(array $upplerIds): array
    {
        if (empty($upplerIds)) {
            return [];
        }

        try {
            $allSellers = UpplerHelper::getAllPaginatedResults(
                fn ($perPage, $page) => $this->upplerSellerService->getSellers($perPage, $page)
            );

            $partnersData = [];
            foreach ($allSellers as $seller) {
                if (\in_array($seller['id'], $upplerIds, true)) {
                    $partnersData[$seller['id']] = [
                        'logo' => $seller['logo'] ?? $seller['avatar_url'] ?? null,
                    ];
                }
            }

            return $partnersData;
        } catch (\Exception $e) {
            $this->apiLogger->error('Erreur lors de la récupération des données partenaires via UpplerSellerService', [
                'error' => $e->getMessage(),
            ]);

            throw new ServiceUnavailableHttpException(null, 'Impossible de récupérer les données des partenaires', $e);
        }
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
