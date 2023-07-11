<?php

declare(strict_types=1);

namespace App\DataProvider;

use ApiPlatform\Core\DataProvider\CollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Entity\Favorite;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\Service\Attribute\Required;

class FavoriteProvider implements RestrictedDataProviderInterface, CollectionDataProviderInterface
{

    #[Required]
    public EntityManagerInterface $em;

    #[Required]
    public RequestStack $requestStack;

    #[Required]
    public Security $security;


    public function getCollection(string $resourceClass, string $operationName = null)
    {
        $account = $this->requestStack->getSession()->get('account');
        return $this->em->getRepository(Favorite::class)->findFavorites($account);
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return Favorite::class === $resourceClass;
    }
}
