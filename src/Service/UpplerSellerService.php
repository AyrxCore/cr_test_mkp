<?php

declare(strict_types=1);

namespace App\Service;

use App\Dto\Seller;
use App\Dto\Price;
use App\Dto\Product;
use App\Dto\Property;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\HttpFoundation\Response;

class UpplerSellerService extends HttpClientProvider
{
    protected string $upplerUrlSourceProductImg;
    protected string $upplerUrlSourceListProductImg;
    protected string $upplerUrlSourceSellerImg;
    protected AdapterInterface $cache;

    public function __construct(
        string $env,
        string $apiUrl,
        string $adminClientId,
        string $adminClientSecret,
        string $adminTokenFile,
        string $httpCachePath,
        string $upplerUrlSourceProductImg,
        string $upplerUrlSourceListProductImg,
        string $upplerUrlSourceSellerImg,
        AdapterInterface $cache
    )
    {
        parent::__construct($env, $apiUrl, $adminClientId, $adminClientSecret, $adminTokenFile, $httpCachePath);
        $this->upplerUrlSourceProductImg = $upplerUrlSourceProductImg;
        $this->upplerUrlSourceListProductImg = $upplerUrlSourceListProductImg;
        $this->upplerUrlSourceSellerImg = $upplerUrlSourceSellerImg;
        $this->cache = $cache;
    }

    public function getSellers($perPage = 16, $page = 1): array | null
    {
        $res = $this->request(
            'POST',
            $this->apiUrl . 'v1/buyer/search/company?perPage=' . $perPage . '&page=' . $page
        );

        dump($res);

        if (Response::HTTP_PARTIAL_CONTENT === $res->getStatusCode() || Response::HTTP_OK === $res->getStatusCode() ) {
            $upplerSellers = json_decode($res->getContent());
            $sellers = [];
            foreach ($upplerSellers->results as $upplerSeller) {
                $sellers[] = $this->hydrateSeller($upplerSeller);
            }
            return $sellers;
        }

        return null;
    }


    public function getSeller(int $sellerId = null): Seller | null
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
        $avatar = !empty($remoteSeller->avatar) ? $remoteSeller->avatar : null;
        $seller->setAvatar($avatar);
        $description = !empty($remoteSeller->description->default) ? $remoteSeller->description->default : null;
        $seller->setDescription($description);

        return $seller;
    }
}
