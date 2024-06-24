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

readonly class SellerProvider implements ProviderInterface
{
    public function __construct(
        private SellerFactory $sellerFactory,
        private UpplerProductService $upplerProductService,
        private UpplerSellerService $upplerSellerService,
    ) {
    }

    /**
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Exception
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): Seller|array|null
    {
        if ($operation instanceof CollectionOperationInterface) {
            try {
                $params = $context['filters'] ?? [];

                $allUpplerSellers = $this->upplerSellerService->getSellers();
                $allAdherentSellers = $this->upplerProductService->findAllSellers(params: $params);

                $sellers = \array_filter($allUpplerSellers['results'], function ($seller) use ($allAdherentSellers) {
                    return \array_key_exists($seller['id'], $allAdherentSellers);
                });

                return $this->sellerFactory->createAndAddToCollection($sellers);
            } catch (\Exception $e) {
                throw new BadRequestHttpException('An error occurred while retrieving the sellers.');
            }
        }

        $seller = $this->upplerSellerService->getSeller($uriVariables['id']);

        return $this->sellerFactory->create($seller);
    }
}
