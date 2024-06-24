<?php

declare(strict_types=1);

namespace App\Doctrine\Extension;

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use App\Entity\Account;
use App\Entity\SavedCart;
use App\Entity\User;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RequestStack;

readonly class SavedCartExtension implements QueryCollectionExtensionInterface, QueryItemExtensionInterface
{
    public function __construct(private Security $security, private RequestStack $requestStack)
    {
    }

    public function applyToCollection(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, Operation $operation = null, array $context = []): void
    {
        $this->addWhere($queryBuilder, $resourceClass);
    }

    public function applyToItem(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, array $identifiers, Operation $operation = null, array $context = []): void
    {
        $this->addWhere($queryBuilder, $resourceClass);
    }

    private function addWhere(QueryBuilder $queryBuilder, string $resourceClass): void
    {
        if (
            $resourceClass !== SavedCart::class
            || $this->security->isGranted('ROLE_ADMIN')
            /** @var User $user */
            || $this->security->getUser() === null
        ) {
            return;
        }

        /** @var Account $account */
        $account = $this->requestStack->getSession()->get('account');

        $rootAlias = $queryBuilder->getRootAliases()[0];
        $queryBuilder->andWhere(\sprintf('%s.account = :current_account', $rootAlias));
        $queryBuilder->setParameter('current_account', $account->getId());
    }
}
