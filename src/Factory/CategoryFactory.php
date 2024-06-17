<?php

declare(strict_types=1);

namespace App\Factory;

use App\Dto\Category;
use App\Entity\Account;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use App\Context\ChannelContext;
use Symfony\Component\Security\Core\Security;

class CategoryFactory extends AbstractFactory
{
    private const CUSTOM_CATEGORY_NAME_KEY = "CUSTOM_CATEGORIES_NAME";
    private array $customCategoriesValues = [];

    public function __construct(private RequestStack $requestStack, protected AdapterInterface $cache, private ChannelContext $channelContext, private Security $security)
    {
        parent::__construct($this->cache);
    }

    public function createAndAddToCollection(array $data): array
    {
        $value = $this->channelContext->getChannel()->getChannelOptionValueByKey(self::CUSTOM_CATEGORY_NAME_KEY);

        $this->customCategoriesValues = $value ? json_decode($value, true) : [];
        
        return parent::createAndAddToCollection($data);
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
        $category->setName(
            $this->customCategoriesValues[$data['id']] ?? 
            (is_array($data['name']) ? $data['name']['default'] : $data['name'])
        );        
        $category->setImage($data['image'] ?? '');
        $category->setParentId($data['parent'] ?? null);
        $category->setProductCount($data['count'] ?? null);
        $category->setChecked($data['checked'] ?? null);
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
