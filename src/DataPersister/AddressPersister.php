<?php

declare(strict_types=1);

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Dto\Address;
use App\Factory\AddressFactory;
use App\Service\UpplerAddressService;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class AddressPersister implements ContextAwareDataPersisterInterface
{
    public function __construct(public UpplerAddressService $upplerAddressService, public AddressFactory $addressFactory)
    {
    }

    public function supports($data, array $context = []): bool
    {
        return $data instanceof Address;
    }

    /**
     * @param Address $data
     */
    public function persist($data, array $context = []): bool|Address
    {
        if (isset($context['item_operation_name']) && $context['item_operation_name'] === 'update') {
            return $this->upplerAddressService->updateAddress($data);
        } elseif (isset($context['collection_operation_name']) && $context['collection_operation_name'] === 'create') {
            return $this->addressFactory->create($this->upplerAddressService->createAddress($data));
        }

        throw new BadRequestHttpException();
    }

    public function remove($data, array $context = [])
    {
        // TODO: Implement remove() method.
    }
}
