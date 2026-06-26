<?php

declare(strict_types=1);

namespace App\Factory;

use App\Dto\Address;

class AddressFactory extends AbstractFactory
{
    public function create(array $data): Address
    {
        $address = new Address();
        $address->setId($data['id'] ?? null);
        $address->setExternalId($data['externalId'] ?? null);
        $address->setFullName($data['fullName'] ?? '');
        $address->setAddress($data['address'] ?? '');
        $address->setShipping(!empty($data['shipping']));
        $address->setBilling(!empty($data['billing']));
        $address->setZipcode($data['zipcode'] ?? '');
        $address->setCity($data['city'] ?? '');
        $address->setCountry($data['country'] ?? '');
        $address->setPhone($data['phone'] ?? null);

        return $address;
    }
}
