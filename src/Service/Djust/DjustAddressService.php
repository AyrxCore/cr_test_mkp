<?php

declare(strict_types=1);

namespace App\Service\Djust;

use App\Dto\Address;
use App\Enum\Djust\DjustApiEndpoint;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DjustAddressService
{
    public function __construct(
        private readonly DjustHttpClientService $djustHttpClient,
    ) {
    }

    public function getAddresses(): array
    {
        return $this->djustHttpClient->get(DjustApiEndpoint::SHOP_ADDRESSES->value);
    }

    /**
     * L'API Djust ne supporte pas GET /addresses/:id.
     * On récupère la collection puis on filtre par ID interne.
     */
    public function getAddress(string $addressId): array
    {
        $allAddresses = $this->getAddresses();

        foreach ($allAddresses as $address) {
            if (isset($address['id']) && $address['id'] === $addressId) {
                return $address;
            }
        }

        throw new NotFoundHttpException(\sprintf('Address with id "%s" not found', $addressId));
    }

    public function createAddress(Address $address): array
    {
        return $this->djustHttpClient->post(
            DjustApiEndpoint::SHOP_ADDRESSES->value,
            $this->buildAddressData($address)
        );
    }

    public function updateAddress(Address $address): array
    {
        $endpoint = \sprintf(DjustApiEndpoint::SHOP_ADDRESS_BY_ID->value, $address->getId());

        return $this->djustHttpClient->put($endpoint, $this->buildAddressData($address));
    }

    public function deleteAddress(string $addressId): array
    {
        $endpoint = \sprintf(DjustApiEndpoint::SHOP_ADDRESS_BY_ID->value, $addressId);

        return $this->djustHttpClient->delete($endpoint);
    }

    private function buildAddressData(Address $address): array
    {
        return [
            'fullName' => $address->getFullName(),
            'address' => $address->getAddress(),
            'zipcode' => $address->getZipCode(),
            'city' => $address->getCity(),
            'country' => $address->getCountry(),
            'phone' => $address->getPhone(),
            'shipping' => $address->isShipping(),
            'billing' => $address->isBilling(),
        ];
    }
}
