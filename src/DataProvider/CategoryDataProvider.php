<?php

declare(strict_types=1);

namespace App\DataProvider;

use ApiPlatform\Core\DataProvider\CollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Dto\Category;
use App\Entity\Account;
use App\Service\UpplerProductService;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class CategoryDataProvider implements RestrictedDataProviderInterface, CollectionDataProviderInterface
{
    public function __construct(private UpplerProductService $upplerProductService, private RequestStack $requestStack, private AdapterInterface $cache)
    {
    }

    public function getCollection(string $resourceClass, string $operationName = null): array
    {
        $session = $this->requestStack->getSession();
        /** @var Account $account */
        $account = $session->get('account');
        $categoryList = $this->cache->getItem('category_'.$account->getId());

        if (!$categoryList->isHit()) {
            $result = $this->upplerProductService->findAllCategories();
            $categories = [];

            foreach ($result as $remoteCategory) {
                $category = new Category();
                $categories[] = $category->hydrate($remoteCategory);
            }

            $categoryList->set($categories);
            $categoryList->expiresAfter(new \DateInterval('PT1H')); // the item will be cached for 1 hour
            $this->cache->save($categoryList);
        }

        return $categoryList->get();
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return $resourceClass === Category::class;
    }
}
