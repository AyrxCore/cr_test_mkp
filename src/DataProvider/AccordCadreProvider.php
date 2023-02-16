<?php

declare(strict_types=1);

namespace App\DataProvider;

use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Dto\AccordCadre;
use App\Service\UpplerAccordCadreService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Contracts\Service\Attribute\Required;


class AccordCadreProvider implements RestrictedDataProviderInterface, ItemDataProviderInterface
{
    #[Required]
    public EntityManagerInterface $em;

    #[Required]
    public UpplerAccordCadreService $upplerAccordCadreService;

    #[Required]
    public RequestStack $requestStack;


    public function getItem(string $resourceClass, $id, string $operationName = null, array $context = [])
    {
        $session = $this->requestStack->getSession();

        return new JsonResponse($this->upplerAccordCadreService->getAccordCadre($id, (string)$session->get('account')->getId()));
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return AccordCadre::class === $resourceClass;
    }
}
