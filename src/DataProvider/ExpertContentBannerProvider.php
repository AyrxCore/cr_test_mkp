<?php

declare(strict_types=1);

namespace App\DataProvider;

use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use App\Dto\ExpertContentBanner;
use App\Service\UpplerDynamicEntityService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Service\Attribute\Required;


class ExpertContentBannerProvider implements ItemDataProviderInterface
{

    #[Required]
    public EntityManagerInterface $em;

    #[Required]
    public UpplerDynamicEntityService $upplerDynamicEntityService;

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return ExpertContentBanner::class === $resourceClass;
    }

    public function getItem(string $resourceClass, $id, string $operationName = null, array $context = [])
    {
        return $this->upplerDynamicEntityService->getDynamicEntityBanner();
    }
}
