<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UpplerSellerService extends AbstractUpplerService
{
    public function getSellers($perPage = 16, $page = 1): array
    {
        $res = $this->request(
            'POST',
            'v1/buyer/search/company?perPage='.$perPage.'&page='.$page
        );

        if ($res->getStatusCode() !== Response::HTTP_OK) {
            throw new NotFoundHttpException('Not found');
        }

        return \json_decode($res->getContent(), true);
    }

    public function getSeller(int $sellerId = null): array
    {
        $res = $this->request(
            'GET',
            'v1/buyer/seller/'.$sellerId
        );

        if ($res->getStatusCode() !== Response::HTTP_OK) {
            throw new NotFoundHttpException('Not found');
        }

        return \json_decode($res->getContent(), true);
    }

    public function getSellerPromotions(int $sellerId): array|null
    {
        $res = $this->request(
            'GET',
            'v1/administrator/promotion/?criteria[owner]='.$sellerId.'&criteria[state]=activated&criteria[model]=shipment_order_min&expand[]=buyer_eligibility&expand[]=order_eligibility&expand[]=order_item_eligibility&expand[]=shipment_eligibility&expand[]=variant_eligibility&expand[]=code',
            [],
            true,
        );
        if ($res->getStatusCode() === Response::HTTP_OK) {
            return \json_decode($res->getContent(), true);
        }

        return null;
    }
}
