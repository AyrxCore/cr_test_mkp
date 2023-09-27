<?php

declare(strict_types=1);

namespace App\DataProvider;

use ApiPlatform\Core\DataProvider\CollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Dto\Country;
use App\Service\UpplerCountryService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Cache\Adapter\AdapterInterface;

class CountryDataProvider implements RestrictedDataProviderInterface, CollectionDataProviderInterface
{
    public function __construct(public EntityManagerInterface $em, public UpplerCountryService $upplerCountryService, private AdapterInterface $cache)
    {
    }

    public function getCollection(string $resourceClass, string $operationName = null): array
    {
        $countryList = $this->cache->getItem('countries');

        if (!$countryList->isHit()) {
            $remoteCountries = $this->upplerCountryService->getCountries();
            $countries = [];

            foreach ($remoteCountries as $remoteCountry) {
                $country = new Country();
                $country->hydrate($remoteCountry);
                $countries[] = $country;
            }

            $countryList->set($countries);
            $countryList->expiresAfter(new \DateInterval('PT1H')); // the item will be cached for 1 hour
            $this->cache->save($countryList);
        }

        return $countryList->get();
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return $resourceClass === Country::class;
    }
}
