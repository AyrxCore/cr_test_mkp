<?php

declare(strict_types=1);

namespace App\State\Provider;

use ApiPlatform\Metadata\CollectionOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Factory\AddressFactory;
use App\Service\Djust\DjustAddressService;

readonly class AddressProvider implements ProviderInterface
{
    public function __construct(
        private AddressFactory $addressFactory,
        private DjustAddressService $djustAddressService,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        if ($operation instanceof CollectionOperationInterface) {
            return $this->addressFactory->createAndAddToCollection($this->djustAddressService->getAddresses());
        }

        $remoteAddress = $this->djustAddressService->getAddress($uriVariables['id']);

        return $this->addressFactory->create($remoteAddress);
    }
}
