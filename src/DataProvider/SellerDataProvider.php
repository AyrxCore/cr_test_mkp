<?php

declare(strict_types=1);

namespace App\DataProvider;

use ApiPlatform\Core\DataProvider\ContextAwareCollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Dto\Seller;
use App\Factory\SellerFactory;
use App\Service\UpplerProductService;
use App\Service\UpplerSellerService;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class SellerDataProvider implements
    RestrictedDataProviderInterface,
    ContextAwareCollectionDataProviderInterface,
    ItemDataProviderInterface
{
    public function __construct(public UpplerSellerService $upplerSellerService, private SellerFactory $sellerFactory, private
    UpplerProductService $upplerProductService)
    {
    }

    /**
     * @throws \Exception
     */
    public function getCollection(string $resourceClass, string $operationName = null, array $context = []): array
    {
        try {
            $allUpplerSellers = $this->upplerSellerService->getSellers();
            $allAdherentSellers = $this->upplerProductService->findAllSellers();

            $sellers = \array_filter($allUpplerSellers['results'], function ($seller) use ($allAdherentSellers) {
                return array_key_exists($seller['id'], $allAdherentSellers);
            });

            return $this->sellerFactory->createAndAddToCollection($sellers);
        } catch (BadRequestHttpException $badRequestException) {
            return [
                'error' => [
                    'message' => 'An error occurred while retrieving the sellers.',
                    'details' => $badRequestException->getMessage(),
                ],
            ];
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage());
        }
    }

    public function getItem(string $resourceClass, $id, string $operationName = null, array $context = []): Seller
    {
        $seller = $this->upplerSellerService->getSeller($id);

        return $this->sellerFactory->create($seller);
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return $resourceClass === Seller::class;
    }
}
