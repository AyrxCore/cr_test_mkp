<?php

declare(strict_types=1);

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Service\Attribute\Required;

Abstract class AbstractUpplerProductService extends HttpClientProvider
{
    protected string $upplerUrlSourceProductImg;
    protected string $upplerUrlSourceListProductImg;
    protected string $upplerUrlSourceSellerImg;
    protected AdapterInterface $cache;
    protected UpplerSellerService $upplerSellerService;
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
        AdapterInterface $cache,
        UpplerSellerService $upplerSellerService
    )
    {
        parent::__construct($env, $apiUrl, $adminClientId, $adminClientSecret, $adminTokenFile, $httpCachePath);
        $this->upplerUrlSourceProductImg = $upplerUrlSourceProductImg;
        $this->upplerUrlSourceListProductImg = $upplerUrlSourceListProductImg;
        $this->upplerUrlSourceSellerImg = $upplerUrlSourceSellerImg;
        $this->cache = $cache;
        $this->upplerSellerService = $upplerSellerService;
    }

    #[Required]
    public RequestStack $requestStack;

    #[Required]
    public EntityManagerInterface $em;

    public function searchProductsByParams(array $options = [], array $filters = [], int $perPage = 5, $page = 1): \stdClass | null
    {
        $session = $this->requestStack->getSession();
        $session->start();

        $urlFilters = null;

        if (!empty($filters)) {
            foreach ($filters as $filter) {
                $urlFilters.= '&expand[]=' . $filter;
            }
        }

        $res = $this->request(
            'POST',
            $this->apiUrl . 'v1/buyer/search/product?page='.$page.'&perPage=' . $perPage . $urlFilters,
            [
                'json' => $options
            ]
        );

        return Response::HTTP_OK === $res->getStatusCode() ? json_decode($res->getContent()) : null;
    }

    /**
     * @param int|null $productId
     * @param array $filters
     * @return mixed
     */
    public function getObject(int $productId = null, array $filters = []): mixed
    {
        $session = $this->requestStack->getSession();
        $session->start();

        $filters =  empty($filters) ? ['price', 'properties', 'variants'] : $filters;
        $urlFilters = null;

        if (!empty($filters)) {
            foreach ($filters as $filter) {
                $urlFilters.= null === $urlFilters ? '?expand[]=' . $filter : '&expand[]=' . $filter;
            }
        }

        $res = $this->request(
            'GET',
            $this->apiUrl . 'v1/buyer/product/' . $productId . $urlFilters
        );

        if (Response::HTTP_OK === $res->getStatusCode()) {

            return json_decode($res->getContent());
        }

        return null;
    }
}
