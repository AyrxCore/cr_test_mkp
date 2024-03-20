<?php

declare(strict_types=1);

namespace App\DataProvider;

use ApiPlatform\Core\DataProvider\CollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Entity\Favorite;
use App\Repository\FavoriteRepository;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class FavoriteProvider implements RestrictedDataProviderInterface, CollectionDataProviderInterface
{
    public function __construct(private FavoriteRepository $favoriteRepository, private RequestStack $requestStack)
    {
    }

    public function getCollection(string $resourceClass, string $operationName = null): array
    {
        $account = $this->requestStack->getSession()->get('account');
        if (!$account) {
            throw new AuthenticationException('You must be logged in to access this resource.');
        }

        return $this->favoriteRepository->findFavorites($account);
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return $resourceClass === Favorite::class;
    }
}
