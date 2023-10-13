<?php

declare(strict_types=1);

namespace App\DataProvider;

use ApiPlatform\Core\DataProvider\CollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Dto\Country;
use App\Factory\CountryFactory;
use App\Service\UpplerCountryService;
use Doctrine\ORM\EntityManagerInterface;

class CountryDataProvider implements RestrictedDataProviderInterface, CollectionDataProviderInterface
{
    public function __construct(public EntityManagerInterface $em, public UpplerCountryService $upplerCountryService, private CountryFactory $countryFactory)
    {
    }

    public function getCollection(string $resourceClass, string $operationName = null): array
    {
        return $this->countryFactory->createAndAddToCollection($this->upplerCountryService->getCountries());
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return $resourceClass === Country::class;
    }
}
