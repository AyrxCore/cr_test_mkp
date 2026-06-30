<?php

declare(strict_types=1);

namespace App\State\Provider;

use ApiPlatform\Metadata\CollectionOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Dto\Seller;
use App\Factory\SellerFactory;
use App\Mapper\DjustSearchParamsMapper;
use App\Service\Account\CurrentAccountProvider;
use App\Service\Djust\DjustSellerService;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

readonly class SellerProvider implements ProviderInterface
{
    public function __construct(
        private DjustSearchParamsMapper $djustSearchParamsMapper,
        private DjustSellerService $djustSellerService,
        private SellerFactory $sellerFactory,
        private CurrentAccountProvider $currentAccountProvider,
    ) {
    }

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
            $customerAccountId = $this->currentAccountProvider->getAccount()?->getDjustCustomerAccountId();
            $params = $this->djustSearchParamsMapper->fromContext($context);
            $sellers = $this->djustSellerService->getValidSellers($customerAccountId, $params);

            return $this->sellerFactory->createAndAddToCollection($sellers);
        } catch (\Exception $e) {
            throw new BadRequestHttpException('An error occurred while retrieving the sellers: '.$e->getMessage());
        }
    }

    private function getSeller(string $sellerId): Seller
    {
        try {
            $customerAccountId = $this->currentAccountProvider->getAccount()?->getDjustCustomerAccountId();
            $seller = $this->djustSellerService->getSeller($sellerId, $customerAccountId);

            if ($seller === null) {
                throw new NotFoundHttpException('Seller not found.');
            }

            return $this->sellerFactory->create($seller);
        } catch (NotFoundHttpException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new NotFoundHttpException('Seller not found.');
        }
    }
}
