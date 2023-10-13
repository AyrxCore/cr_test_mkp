<?php

declare(strict_types=1);

namespace App\Factory;

use App\Dto\Category;
use App\Entity\Account;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class CountryFactory extends AbstractFactory
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

        $categoryCached = $this->cache->getItem(\sprintf('country_%d_account_%s', $data['id'], $account->getId()->toRfc4122()));
        if (!$categoryCached->isHit()) {
            $country = new \App\Dto\Country();
            $country->setId($data['id']);
            $country->setName($data['name']);

            $categoryCached->set($country);
            $categoryCached->expiresAfter(new \DateInterval('P1D')); // the item will be cached for 10 seconds
            $this->cache->save($categoryCached);
        }

        return $categoryCached->get();
    }
}
