<?php

declare(strict_types=1);

namespace App\DataProvider;

use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Dto\Address;
use App\Dto\SubAccount;
use App\Entity\Account;
use App\Service\UpplerAccountService;
use App\Service\UpplerBuyerCompanyService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Service\Attribute\Required;


class SubAccountProvider implements RestrictedDataProviderInterface, ItemDataProviderInterface
{

    #[Required]
    public EntityManagerInterface $em;

    #[Required]
    public UpplerAccountService $upplerAccountService;

    public function getItem(string $resourceClass, $id, string $operationName = null, array $context = [])
    {
        $account = $this->upplerAccountService->getUserSubAccountDatas();

        return $account;
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return SubAccount::class === $resourceClass;
    }

}
