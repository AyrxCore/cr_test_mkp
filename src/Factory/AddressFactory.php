<?php

declare(strict_types=1);

namespace App\Factory;

use App\Dto\Address;

class AddressFactory extends AbstractFactory
{
    public function create(array $data): Address
    {
        $address = new Address();
        $address->setId($data['id']);
        $address->setName($data['name']);
        $address->setCompanyId($data['companies'][0]['id']);
        $address->setCompany($data['companies'][0]['name']);
        $address->setStreet($data['street']);
        $address->setType($data['type']);
        $address->setPostCode($data['postcode']);
        $address->setCity($data['city']);
        $address->setCountry($data['country']['id']);
        $address->setLastName($data['last_name']);
        $address->setFirstName($data['first_name']);
        $address->setPhone($data['phone']);

        return $address;
    }
}
