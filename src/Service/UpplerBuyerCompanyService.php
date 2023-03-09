<?php

declare(strict_types=1);

namespace App\Service;

use App\Dto\Address;
use App\Entity\Account;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Service\Attribute\Required;

class UpplerBuyerCompanyService extends HttpClientProvider
{

    #[Required]
    public RequestStack $requestStack;

    #[Required]
    public EntityManagerInterface $em;

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
                $urlFilters .= null === $urlFilters ? '?expand[]=' . $filter : '&expand[]=' . $filter;
            }
        }

        $res = $this->request(
            'GET',
            $this->apiUrl . 'v1/administrator/buyer/' . $id . $urlFilters,
            [],
            true
        );
        if (Response::HTTP_OK === $res->getStatusCode()) {
            return json_decode($res->getContent());
        }

        return null;
    }

    public function getAdresses(): array|null
    {
        $session = $this->requestStack->getSession();
        $session->start();
        $urlFilters = null;

        $res = $this->request(
            'GET',
            $this->apiUrl . 'v1/buyer/company-address',
            []
        );
        if (Response::HTTP_OK === $res->getStatusCode()) {
            $addresses = json_decode($res->getContent());
            $this->computeAdresses($addresses);
            return $addresses;
        }

        return null;
    }

    public function getAddress(?int $addressId = null, string $url = null): \stdClass|null
    {
        $session = $this->requestStack->getSession();
        $session->start();

        $res = $this->request(
            'GET',
            null !== $url ? $url : $this->apiUrl . 'v1/administrator/company-address/' . $addressId,
            [],
            true
        );

        if (Response::HTTP_OK === $res->getStatusCode()) {
            $address = json_decode($res->getContent());
            $this->computeAddress($address);
            return $address;
        }

        return null;
    }

    private function computeAdresses(array &$addresses): void
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

    public function createAddress(Address $address)
    {
        $res = $this->request(
            'POST',
            $this->apiUrl . 'v1/administrator/company-address/',
            [
                'json' => [
                    'name'       => $address->getName(),
                    'type'       => $address->getType(),
                    'first_name' => $address->getFirstName(),
                    'last_name'  => $address->getLastName(),
                    'street'     => $address->getStreet(),
                    'postcode'   => $address->getPostCode(),
                    'city'       => $address->getCity(),
                    'country'    => $address->getCountry(),
                    'company'    => $address->getCompany(),
                    'companies'  => [$address->getCompanyId()],
                    'phone'      => $address->getPhone(),
                ],
            ],
            true
        );
        if (Response::HTTP_CREATED === $res->getStatusCode()) {
            $headers = $res->getHeaders();
            $address = $this->getAddress(null, $headers["location"][0]);
            return $address;
        }

        return null;
    }

    public function updateAddress(Address $address)
    {
        $res = $this->request(
            'PATCH',
            $this->apiUrl . 'v1/administrator/company-address/' . $address->getId(),
            [
                'json' => [
                    'name'       => $address->getName(),
                    'type'       => $address->getType(),
                    'first_name' => $address->getFirstName(),
                    'last_name'  => $address->getLastName(),
                    'street'     => $address->getStreet(),
                    'postcode'   => $address->getPostCode(),
                    'city'       => $address->getCity(),
                    'country'    => $address->getCountry(),
                    'company'    => $address->getCompany(),
                    'phone'      => $address->getPhone(),
                ],
            ],
            true
        );
        if (Response::HTTP_OK === $res->getStatusCode()) {
            $addresses = json_decode($res->getContent());
            $this->computeAdresses($addresses);
            return $addresses;
        }

        return null;
    }

    public function getUserBuyerDatas(): object|null
    {
        $session = $this->requestStack->getSession();
        /**@var Account $account */
        $account = $session->get('account');
        $res = $this->request(
            'GET',
            $this->apiUrl . 'v1/buyer/profile/' . $account->getUpplerCompanyId() . '?expand[]=address'
        );
        if (Response::HTTP_OK === $res->getStatusCode()) {
            return json_decode($res->getContent());
        }

        return null;
    }

}
