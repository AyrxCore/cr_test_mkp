<?php

declare(strict_types=1);

namespace App\Service;

use App\Dto\Seller;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class UpplerSellerService extends AbstractUpplerService
{
    private const IMG_PATH = 'https://uppler-platform-quantis.s3.eu-west-3.amazonaws.com/image/';

    public function __construct(
        HttpClientInterface $upplerClient,
        RequestStack $requestStack,
        string $upplerEnv,
        string $adminClientId,
        string $adminClientSecret,
        string $adminTokenFile,
        string $httpCachePath,
        private AdapterInterface $cache
    ) {
        parent::__construct(
            upplerClient: $upplerClient,
            requestStack: $requestStack,
            upplerEnv: $upplerEnv,
            adminClientId: $adminClientId,
            adminClientSecret: $adminClientSecret,
            adminTokenFile: $adminTokenFile,
            httpCachePath: $httpCachePath,
        );
    }

    public function getSellers($perPage = 16, $page = 1): array|null
    {
        $res = $this->request(
            'POST',
            'v1/buyer/search/company?perPage='.$perPage.'&page='.$page
        );

        if ($res->getStatusCode() === Response::HTTP_PARTIAL_CONTENT || $res->getStatusCode() === Response::HTTP_OK) {
            $upplerSellers = \json_decode($res->getContent());
            $sellers = [];
            foreach ($upplerSellers->results as $upplerSeller) {
                $sellers[] = $this->hydrateSeller($upplerSeller);
            }

            return $sellers;
        }

        return null;
    }

    public function getSeller(int $sellerId = null): Seller|null
    {
        $item = $this->cache->getItem('seller_'.$sellerId);

        if ($item->isHit()) {
            return $item->get();
        }

        $session = $this->requestStack->getSession();
        $session->start();

        $res = $this->request(
            'GET',
            'v1/buyer/seller/'.$sellerId
        );

        if ($res->getStatusCode() === Response::HTTP_OK) {
            $upplerSeller = \json_decode($res->getContent());

            $item->set($this->hydrateSeller($upplerSeller));
            $item->expiresAfter(new \DateInterval('P1D')); // the item will be cached for 10 seconds
            $this->cache->save($item);

            return $item->get();
        }

        return null;
    }

    public function hydrateSeller($remoteSeller): Seller
    {
        $seller = new Seller();
        $seller->setId($remoteSeller->id);
        $seller->setName($remoteSeller->name);
        $description = !empty($remoteSeller->description->default) ? $remoteSeller->description->default : null;
        $seller->setDescription($description);
        $avatar = null;
        if (!empty($remoteSeller->avatar_url)) {
            $avatar = $remoteSeller->avatar_url;
        } elseif (!empty($remoteSeller->avatar)) {
            $avatar = self::IMG_PATH.$remoteSeller->avatar;
        }
        $seller->setAvatar($avatar);
        if (isset($remoteSeller->tos)) {
            $seller->setTos(\json_decode(\json_encode($remoteSeller->tos), true));
        }

        return $seller;
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
