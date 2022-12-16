<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Account;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Service\Attribute\Required;

class UpplerCompanyService extends HttpClientProvider
{
    #[Required]
    public RequestStack $requestStack;

    #[Required]
    public EntityManagerInterface $em;

    // filters : filtres disponibles pour étendre la quantité d'informations retournées
    // Valeurs possibles ("accounts","files","subscriptions","dynamicFields")
    // https://app.preprod-yousg3q-qbpekzlwwankw.fr-3.platformsh.site/api-documentation/operator#section-Company
    public function getCompany(int $id, array $filters = []): object | null
    {
        $session = $this->requestStack->getSession();
        $session->start();
        $urlFilters = null;

        if (!empty($filters)) {
            foreach ($filters as $filter) {
                $urlFilters.= null === $urlFilters ? '?expand[]=' . $filter : '&expand[]=' . $filter;
            }
        }

        $res = $this->request(
            'GET',
            $this->apiUrl . 'v1/administrator/buyer/' . $id. $urlFilters,
            [],
            true
        );
        if (Response::HTTP_OK === $res->getStatusCode()) {
            return json_decode($res->getContent());
        }

        return null;
    }

    public function getAdresses(): array | null
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
            return $this->computeAdresses($addresses);
        }

        return null;
    }

    private function computeAdresses(array &$addresses)
    {
        foreach ($addresses as $address) {
            $country = $address->country->name->fr;
            $address->country = $country;
            unset($address->companies);
            unset($address->first_name);
            unset($address->last_name);
            unset($address->external_id);
            unset($address->phone);
            unset($address->email);
        }

        return $addresses;
    }

}
