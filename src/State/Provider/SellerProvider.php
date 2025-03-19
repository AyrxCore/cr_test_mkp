<?php

declare(strict_types=1);

namespace App\State\Provider;

use ApiPlatform\Metadata\CollectionOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Dto\Seller;
use App\Factory\SellerFactory;
use App\Service\UpplerProductService;
use App\Service\UpplerSellerService;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;

readonly class SellerProvider implements ProviderInterface
{
    public function __construct(
        private SellerFactory $sellerFactory,
        private UpplerProductService $upplerProductService,
        private UpplerSellerService $upplerSellerService,
    ) {
    }

    /**
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     * @throws \Exception
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): Seller|array|null
    {
        if ($operation instanceof CollectionOperationInterface) {
            return $this->getSellers($context);
        }

        return $this->getSeller($uriVariables['id']);
    }

    private function getSellers(array $context = []): array
    {
        try {
            $params = $context['filters'] ?? [];
            $allUpplerSellers = $this->upplerSellerService->getSellers();
            $allAdherentSellers = $this->upplerProductService->findAllSellers(params: $params);

            $sellers = \array_filter($allUpplerSellers['results'], function ($seller) use ($allAdherentSellers) {
                return \array_key_exists($seller['id'], $allAdherentSellers);
            });

            return $this->sellerFactory->createAndAddToCollection($sellers);
        } catch (\Exception $e) {
            throw new BadRequestHttpException('An error occurred while retrieving the sellers: '.$e->getMessage());
        }
    }

    private function getSeller(int $sellerId): Seller
    {
        try {
            $seller = $this->upplerSellerService->getSeller($sellerId);

            return $this->sellerFactory->create($seller);
        } catch (\Exception $e) {
            throw new NotFoundHttpException('Seller not found.');
        }
    }
}
