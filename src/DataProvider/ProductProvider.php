<?php

declare(strict_types=1);

namespace App\DataProvider;

use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Dto\Product;
use App\Service\UpplerProductService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\Service\Attribute\Required;


class ProductProvider implements RestrictedDataProviderInterface, ItemDataProviderInterface
{
    public function __construct(private AdapterInterface $cache)
    {
    }

    #[Required]
    public EntityManagerInterface $em;

    #[Required]
    public UpplerProductService $upplerProductService;

    public function getItem(string $resourceClass, $id, string $operationName = null, array $context = [])
    {
        $item = $this->cache->getItem('product_' . $id);
        if (!$item->isHit()) {
            $product = $this->upplerProductService->getProduct($id);
            $item->set($product);
            $item->expiresAfter(new \DateInterval('P1D')); // the item will be cached for 10 seconds
            $this->cache->save($item);

        }
        return new JsonResponse($item->get());
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return Product::class === $resourceClass;
    }
}
