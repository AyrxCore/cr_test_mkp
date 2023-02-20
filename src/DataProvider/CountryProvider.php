<?php

declare(strict_types=1);

namespace App\DataProvider;

use ApiPlatform\Core\DataProvider\CollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Dto\Address;
use App\Dto\Country;
use App\Entity\Account;
use App\Service\UpplerBuyerCompanyService;
use App\Service\UpplerRepositoryService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Service\Attribute\Required;


class CountryProvider implements RestrictedDataProviderInterface, CollectionDataProviderInterface
{
    #[Required]
    public EntityManagerInterface $em;

    #[Required]
    public UpplerRepositoryService $upplerRepositoryService;

    public function getCollection(string $resourceClass, string $operationName = null)
    {
        $countries = $this->upplerRepositoryService->getCountries();

        return $countries;
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return Country::class === $resourceClass;
    }
}
