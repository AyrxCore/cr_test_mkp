<?php

declare(strict_types=1);

namespace App\Factory;

use App\Dto\Country;
use App\Entity\Account;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class CountryFactory extends AbstractFactory
{
    public function __construct(private RequestStack $requestStack, protected AdapterInterface $cache)
    {
        parent::__construct($this->cache);
    }

    public function create(array $data): Country
    {
        $session = $this->requestStack->getSession();
        /** @var Account $account */
        $account = $session->get('account');

        $countryCached = $this->cache->getItem(\sprintf('country_%d_account_%s', $data['id'], $account->getId()->toRfc4122()));
        if (!$countryCached->isHit()) {
            $country = new Country();
            $country->setId($data['id']);
            $country->setName($data['name']['default']);

            $countryCached->set($country);
            $countryCached->expiresAfter(new \DateInterval('P1D')); // the item will be cached for 1 day
            $this->cache->save($countryCached);
        }

        return $countryCached->get();
    }
}
