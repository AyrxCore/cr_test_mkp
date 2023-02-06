<?php

declare(strict_types=1);

namespace App\DataProvider;

use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Dto\AccordCadre;
use App\Dto\Product;
use App\Service\UpplerAccordCadreService;
use App\Service\UpplerProductService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\Service\Attribute\Required;


class AccordCadreProvider implements RestrictedDataProviderInterface, ItemDataProviderInterface
{
    #[Required]
    public EntityManagerInterface $em;

    #[Required]
    public UpplerAccordCadreService $upplerAccordCadreService;

    public function getItem(string $resourceClass, $id, string $operationName = null, array $context = [])
    {
        return new JsonResponse($this->upplerAccordCadreService->getAccordCadre($id));
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return AccordCadre::class === $resourceClass;
    }
}
