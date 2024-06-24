<?php

declare(strict_types=1);

namespace App\State\Provider;

use ApiPlatform\Metadata\CollectionOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Repository\FavoriteRepository;
use Symfony\Component\HttpFoundation\RequestStack;

readonly class FavoriteProvider implements ProviderInterface
{
    public function __construct(private FavoriteRepository $favoriteRepository, private RequestStack $requestStack)
    {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        if ($operation instanceof CollectionOperationInterface) {
            $account = $this->requestStack->getSession()->get('account');

            return $this->favoriteRepository->findFavorites($account);
        }

        return $this->favoriteRepository->find($uriVariables['id']);
    }
}
