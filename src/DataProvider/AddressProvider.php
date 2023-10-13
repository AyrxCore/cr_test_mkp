<?php

declare(strict_types=1);

namespace App\DataProvider;

use ApiPlatform\Core\DataProvider\CollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Dto\Address;
use App\Factory\AddressFactory;
use App\Service\UpplerAddressService;

class AddressProvider implements RestrictedDataProviderInterface, ItemDataProviderInterface, CollectionDataProviderInterface
{
    public function __construct(private UpplerAddressService $upplerAddressService, private AddressFactory $addressFactory)
    {
    }

    public function getCollection(string $resourceClass, string $operationName = null): array
    {
        return $this->addressFactory->createAndAddToCollection($this->upplerAddressService->getAdresses());
    }

    public function getItem(string $resourceClass, $id, string $operationName = null, array $context = []): Address
    {
        $remoteAddress = $this->upplerAddressService->getAddress($id);

        return $this->addressFactory->create($remoteAddress);
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return $resourceClass === Address::class;
    }
}
