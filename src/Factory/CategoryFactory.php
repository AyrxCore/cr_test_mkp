<?php

declare(strict_types=1);

namespace App\Factory;

use App\Dto\Category;
use App\Entity\Account;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class CategoryFactory extends AbstractFactory
{
    public function __construct(private RequestStack $requestStack, protected AdapterInterface $cache)
    {
        parent::__construct($this->cache);
    }

    public function create(array $data): Category
    {
        $session = $this->requestStack->getSession();
        /** @var Account $account */
        $account = $session->get('account');

        $categoryCached = $this->cache->getItem(\sprintf('category_%d_account_%s', $data['id'], $account->getId()->toRfc4122()));
        if (!$categoryCached->isHit()) {
            $category = $this->hydrate($data);

            $categoryCached->set($category);
            $categoryCached->expiresAfter(new \DateInterval('P1D')); // the item will be cached for 10 seconds
            $this->cache->save($categoryCached);
        }

        return $categoryCached->get();
    }

    private function hydrate(array $data): Category
    {
        $category = new Category();
        $category->setId($data['id']);
        $category->setName($data['name']);
        $category->setImage($data['image'] ?? '');
        $category->setParentId($data['parent']);
        $category->setProductCount($data['count']);
        $category->setChecked($data['checked']);
        $children = [];

        if (!empty($data['child'])) {
            foreach ($data['child'] as $childData) {
                $children[] = $this->hydrate($childData);
            }
        }

        $category->setChildren($children);

        return $category;
    }
}
