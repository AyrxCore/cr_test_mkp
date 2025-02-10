<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Component\HttpFoundation\Response;

class UpplerBuyerCompanyService extends AbstractUpplerService
{
    // filters : filtres disponibles pour étendre la quantité d'informations retournées
    // Valeurs possibles ("accounts","files","subscriptions","dynamicFields")
    // https://app.preprod-yousg3q-qbpekzlwwankw.fr-3.platformsh.site/api-documentation/operator#section-Company
    public function getBuyerByCompanyId(int $id, array $filters = []): ?object
    {
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

        if ($res && $res->getStatusCode() === Response::HTTP_OK) {
            return \json_decode($res->getContent());
        }

        throw new \Exception('Error while getting buyer company');
    }

    public function getExistingMandates(): array
    {
        $res = $this->request(
            'GET',
            'v1/buyer/mandate/',
        );

        $mandates = [];
        if ($res && $res->getStatusCode() === Response::HTTP_OK) {
            $mandates = \json_decode($res->getContent(), true);
        }

        return $mandates;
    }

    public function getUserBuyerData(): ?object
    {
        $account = $this->getAccount();
        $res = $this->request(
            'GET',
            'v1/buyer/profile/'.$account->getUpplerCompanyId().'?expand[]=address'
        );
        if ($res->getStatusCode() === Response::HTTP_OK) {
            return \json_decode($res->getContent());
        }

        return null;
    }
}
