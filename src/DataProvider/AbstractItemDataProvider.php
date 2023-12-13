<?php

declare(strict_types=1);

namespace App\DataProvider;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGenerator;
use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use Doctrine\Persistence\ManagerRegistry;

abstract class AbstractItemDataProvider extends AbstractDataProvider implements ItemDataProviderInterface
{
    public function __construct(
        protected ManagerRegistry $managerRegistry,
        protected iterable $itemExtensions,
    ) {
        parent::__construct($managerRegistry);
    }

    public function getItem(string $resourceClass, $id, string $operationName = null, array $context = [])
    {
        $queryBuilder = $this->createQueryBuilder();
        [$rootAlias] = $queryBuilder->getRootAliases();

        $queryNameGenerator = new QueryNameGenerator();

        $parameterName = $queryNameGenerator->generateParameterName('id');
        $queryBuilder
            ->where(\sprintf('%s.id = :%s', $rootAlias, $parameterName))
            ->setParameter($parameterName, $id);

        /** @var QueryItemExtensionInterface $extension */
        foreach ($this->itemExtensions as $extension) {
            $extension->applyToItem($queryBuilder, $queryNameGenerator, $resourceClass, ['id' => $id], $operationName, $context);
        }

        return $queryBuilder->getQuery()->getOneOrNullResult();
    }
}
