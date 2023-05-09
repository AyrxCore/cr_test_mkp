<?php

declare(strict_types=1);

namespace App\DataProvider;

use ApiPlatform\Core\DataProvider\CollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Entity\SavedCart;
use App\Service\SavedCartService;
use Doctrine\ORM\EntityManagerInterface;
use Http\Discovery\Exception\NotFoundException;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\Service\Attribute\Required;

class SavedCartProvider implements RestrictedDataProviderInterface, CollectionDataProviderInterface
{

    #[Required]
    public EntityManagerInterface $em;

    #[Required]
    public SavedCartService $savedCartService;

    #[Required]
    public RequestStack $requestStack;

    #[Required]
    public Security $security;


    public function getCollection(string $resourceClass, string $operationName = null): iterable|string
    {
        $session = $this->requestStack->getSession();
        if (!$session->get('account')) {
            throw new AuthenticationException();
        }

        try {
            return $this->savedCartService->getSavedCarts($session->get('account'));
        } catch (NotFoundException $exception) {
            return $exception->getMessage();
        }

    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return SavedCart::class === $resourceClass;
    }
}
