<?php

declare(strict_types=1);

namespace App\State\Provider;

use ApiPlatform\Metadata\CollectionOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Factory\AddressFactory;
use App\Service\UpplerAddressService;

readonly class AddressProvider implements ProviderInterface
{
    public function __construct(
        private AddressFactory $addressFactory,
        private UpplerAddressService $upplerAddressService
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        if ($operation instanceof CollectionOperationInterface) {
            return $this->addressFactory->createAndAddToCollection($this->upplerAddressService->getAdresses());
        }

        $remoteAddress = $this->upplerAddressService->getAddress($uriVariables['id']);

        return $this->addressFactory->create($remoteAddress);
    }
}
