<?php

declare(strict_types=1);

namespace App\DataProvider;

use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Dto\Address;
use App\Entity\Account;
use App\Service\UpplerCompanyService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Service\Attribute\Required;


class AdressProvider implements RestrictedDataProviderInterface, ItemDataProviderInterface
{
    #[Required]
    public EntityManagerInterface $em;

    #[Required]
    public UpplerCompanyService $upplerCompanyService;

    public function getItem(string $resourceClass, $id, string $operationName = null, array $context = [])
    {
        $address = $this->upplerCompanyService->getAddress($id);

        return $address;
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return Address::class === $resourceClass;
    }
}
