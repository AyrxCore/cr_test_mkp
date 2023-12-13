<?php

declare(strict_types=1);

namespace App\Doctrine\Extension;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use App\Context\ChannelContext;
use App\Entity\Account;
use App\Entity\User;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Security\Core\Security;

class CurrentUserAccountExtension implements QueryCollectionExtensionInterface, QueryItemExtensionInterface
{
    public function __construct(private Security $security, private ChannelContext $channelContext)
    {
    }

    /** @noinspection PhpHierarchyChecksInspection */
    public function applyToCollection(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, string $operationName = null): void
    {
        $this->addWhere($queryBuilder, $queryNameGenerator, $resourceClass);
    }

    /** @noinspection PhpHierarchyChecksInspection */
    public function applyToItem(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, array $identifiers, string $operationName = null, array $context = []): void
    {
        $this->addWhere($queryBuilder, $queryNameGenerator, $resourceClass);
    }

    private function addWhere(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass): void
    {
        if (
            $resourceClass !== Account::class
            || $this->security->isGranted('ROLE_ADMIN')
            /** @var User $user */
            || null === $user = $this->security->getUser()
        ) {
            return;
        }

        // user with ROLE_API can view every accounts of current channel
        if ($this->security->isGranted('ROLE_API')) {
            return;
        }

        [$rootAlias] = $queryBuilder->getRootAliases();
        $adherentAlias = $queryNameGenerator->generateJoinAlias('adherent');
        $channelParameterName = $queryNameGenerator->generateParameterName('channel');

        $queryBuilder
            ->join(\sprintf('%s.adherent', $rootAlias), $adherentAlias)
            ->andWhere(\sprintf('%s.channel = :%s', $adherentAlias, $channelParameterName))
            ->setParameter($channelParameterName, $this->channelContext->getChannel()->getId());

        $currentUserParameterName = $queryNameGenerator->generateParameterName('current_user');

        $queryBuilder
            ->andWhere(\sprintf('%s.user = :%s', $rootAlias, $currentUserParameterName))
            ->setParameter($currentUserParameterName, $user->getId());
    }
}
