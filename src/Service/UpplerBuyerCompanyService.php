<?php

declare(strict_types=1);

namespace App\Service;

use App\Dto\Address;
use App\Entity\Account;
use Symfony\Component\HttpFoundation\Response;

class UpplerBuyerCompanyService extends AbstractUpplerService
{
    // filters : filtres disponibles pour étendre la quantité d'informations retournées
    // Valeurs possibles ("accounts","files","subscriptions","dynamicFields")
    // https://app.preprod-yousg3q-qbpekzlwwankw.fr-3.platformsh.site/api-documentation/operator#section-Company
    public function getBuyerByCompanyId(int $id, array $filters = []): object|null
    {
        $session = $this->requestStack->getSession();
        $session->start();
        $urlFilters = null;

        if (!empty($filters)) {
            foreach ($filters as $filter) {
                $urlFilters .= $urlFilters === null ? '?expand[]='.$filter : '&expand[]='.$filter;
            }
        }

        $res = $this->request(
            'GET',
            'v1/administrator/buyer/'.$id.$urlFilters,
            isAdmin: true
        );

        if ($res->getStatusCode() === Response::HTTP_OK) {
            return \json_decode($res->getContent());
        }

        return null;
    }

    public function getAdresses(): array|null
    {
        $res = $this->request('GET', 'v1/buyer/company-address');

        if ($res->getStatusCode() === Response::HTTP_OK) {
            $addresses = \json_decode($res->getContent());
            $this->computeAddresses($addresses);

            return $addresses;
        }

        return null;
    }

    public function getAddress(?int $addressId = null, string $url = null): \stdClass|null
    {
        $res = $this->request(
            'GET',
            $url !== null ? $url : 'v1/administrator/company-address/'.$addressId,
            isAdmin: true
        );

        if ($res->getStatusCode() === Response::HTTP_OK) {
            $address = \json_decode($res->getContent());
            $this->computeAddress($address);

            return $address;
        }

        return null;
    }

    public function createAddress(Address $address)
    {
        $res = $this->request(
            'POST',
            'v1/administrator/company-address/',
            [
                'json' => [
                    'name' => $address->getName(),
                    'type' => $address->getType(),
                    'first_name' => $address->getFirstName(),
                    'last_name' => $address->getLastName(),
                    'street' => $address->getStreet(),
                    'postcode' => $address->getPostCode(),
                    'city' => $address->getCity(),
                    'country' => $address->getCountry(),
                    'company' => $address->getCompany(),
                    'companies' => [$address->getCompanyId()],
                    'phone' => $address->getPhone(),
                ],
            ],
            isAdmin: true
        );
        if ($res->getStatusCode() === Response::HTTP_CREATED) {
            $headers = $res->getHeaders();
            $address = $this->getAddress(null, $headers['location'][0]);

            return $address;
        }

        return null;
    }

    public function updateAddress(Address $address)
    {
        $res = $this->request(
            'PATCH',
            'v1/administrator/company-address/'.$address->getId(),
            [
                'json' => [
                    'name' => $address->getName(),
                    'type' => $address->getType(),
                    'first_name' => $address->getFirstName(),
                    'last_name' => $address->getLastName(),
                    'street' => $address->getStreet(),
                    'postcode' => $address->getPostCode(),
                    'city' => $address->getCity(),
                    'country' => $address->getCountry(),
                    'company' => $address->getCompany(),
                    'phone' => $address->getPhone(),
                ],
            ],
            true
        );
        if ($res->getStatusCode() === Response::HTTP_OK) {
            $addresses = \json_decode($res->getContent());
            $this->computeAddresses($addresses);

            return $addresses;
        }

        return null;
    }

    public function getUserBuyerDatas(): object|null
    {
        $session = $this->requestStack->getSession();
        /** @var Account $account */
        $account = $session->get('account');
        $res = $this->request(
            'GET',
            'v1/buyer/profile/'.$account->getUpplerCompanyId().'?expand[]=address'
        );
        if ($res->getStatusCode() === Response::HTTP_OK) {
            return \json_decode($res->getContent());
        }

        return null;
    }

    private function computeAddresses(array &$addresses): void
    {
        foreach ($addresses as $address) {
            $this->computeAddress($address);
        }
    }

    private function computeAddress(&$address): void
    {
        $country = $address->country->id;
        $address->country = $country;
        unset($address->companies);
        unset($address->external_id);
        unset($address->email);
    }
}
