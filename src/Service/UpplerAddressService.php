<?php

declare(strict_types=1);

namespace App\Service;

use App\Dto\Address;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UpplerAddressService extends AbstractUpplerService
{
    public function createAddress(Address $address): array
    {
        $res = $this->request(
            method: 'POST',
            path: 'v1/administrator/company-address/',
            options: $this->buildJsonData($address, true),
            isAdmin: true
        );

        if ($res->getStatusCode() !== Response::HTTP_CREATED) {
            throw new BadRequestHttpException();
        }

        $headers = $res->getHeaders();

        return $this->getAddress(null, $headers['location'][0]);
    }

    public function getAddress(?int $addressId = null, ?string $url = null): array
    {
        $res = $this->request(
            'GET',
            $url !== null ? $url : 'v1/administrator/company-address/'.$addressId,
            isAdmin: true
        );

        if ($res->getStatusCode() !== Response::HTTP_OK) {
            throw new NotFoundHttpException();
        }

        return \json_decode($res->getContent(), true);
    }

    public function updateAddress(Address $address): void
    {
        $res = $this->request(
            method: 'PATCH',
            path: 'v1/administrator/company-address/'.$address->getId(),
            options: $this->buildJsonData($address),
            isAdmin: true
        );

        if ($res->getStatusCode() !== Response::HTTP_NO_CONTENT) {
            throw new BadRequestHttpException();
        }
    }

    private function buildJsonData(Address $address, bool $operationCreate = false): array
    {
        $json = [
            'json' => [
                'name' => $address->getFullName(),
                'type' => $address->getType(),
                'first_name' => $address->getFirstName(),
                'last_name' => $address->getLastName(),
                'street' => $address->getAddress(),
                'postcode' => $address->getPostCode(),
                'city' => $address->getCity(),
                'country' => $address->getCountry(),
                'company' => $address->getCompany(),
                'phone' => $address->getPhone(),
            ],
        ];

        if ($operationCreate) {
            $json['json']['companies'] = [$address->getCompanyId()];
        }

        return $json;
    }
}
