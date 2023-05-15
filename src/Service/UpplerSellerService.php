<?php

declare(strict_types=1);

namespace App\Service;

use App\Dto\Seller;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\HttpFoundation\Response;

class UpplerSellerService extends HttpClientProvider
{

    private const IMG_PATH = 'https://uppler-platform-quantis.s3.eu-west-3.amazonaws.com/image/';

    protected AdapterInterface $cache;

    public function __construct(
        string $env,
        string $apiUrl,
        string $adminClientId,
        string $adminClientSecret,
        string $adminTokenFile,
        string $httpCachePath,
        AdapterInterface $cache
    ) {
        parent::__construct($env, $apiUrl, $adminClientId, $adminClientSecret, $adminTokenFile, $httpCachePath);
        $this->cache = $cache;
    }

    public function getSellers($perPage = 16, $page = 1): array|null
    {
        $res = $this->request(
            'POST',
            $this->apiUrl . 'v1/buyer/search/company?perPage=' . $perPage . '&page=' . $page
        );

        if (Response::HTTP_PARTIAL_CONTENT === $res->getStatusCode() || Response::HTTP_OK === $res->getStatusCode()) {
            $upplerSellers = json_decode($res->getContent());
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
        $item = $this->cache->getItem('seller_' . $sellerId);

        if ($item->isHit()) {
            return $item->get();
        }

        $session = $this->requestStack->getSession();
        $session->start();

        $res = $this->request(
            'GET',
            $this->apiUrl . 'v1/buyer/seller/' . $sellerId
        );

        if (Response::HTTP_OK === $res->getStatusCode()) {
            $upplerSeller = json_decode($res->getContent());

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
            $avatar = self::IMG_PATH . $remoteSeller->avatar;
        }
        $seller->setAvatar($avatar);
        if (isset($remoteSeller->tos)) {
            $seller->setTos(json_decode(json_encode($remoteSeller->tos), true));
        }
        return $seller;
    }

    public function getSellerPromotions(int $sellerId): array|null
    {
        $res = $this->request(
            'GET',
            $this->apiUrl . 'v1/administrator/promotion/?criteria[owner]=' . $sellerId . '&criteria[state]=activated&criteria[model]=shipment_order_min&expand[]=buyer_eligibility&expand[]=order_eligibility&expand[]=order_item_eligibility&expand[]=shipment_eligibility&expand[]=variant_eligibility&expand[]=code',
            [],
            true,
        );
        if (Response::HTTP_OK === $res->getStatusCode()) {
            return json_decode($res->getContent(), true);
        }
        return null;
    }
}
