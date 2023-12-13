<?php

declare(strict_types=1);

namespace App\DataProvider;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\ContextAwareQueryCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGenerator;
use ApiPlatform\Core\DataProvider\ContextAwareCollectionDataProviderInterface;
use Doctrine\Persistence\ManagerRegistry;

abstract class AbstractCollectionDataProvider extends AbstractDataProvider implements ContextAwareCollectionDataProviderInterface
{
    public function __construct(
        protected ManagerRegistry $managerRegistry,
        protected iterable $collectionExtensions,
    ) {
        parent::__construct($this->managerRegistry);
    }

    public function getCollection(string $resourceClass, string $operationName = null, array $context = []): iterable
    {
        $queryBuilder = $this->createQueryBuilder();

        /** @var ContextAwareQueryCollectionExtensionInterface $extension */
        foreach ($this->collectionExtensions as $extension) {
            $extension->applyToCollection($queryBuilder, new QueryNameGenerator(), $resourceClass, $operationName);
        }

        foreach ($queryBuilder->getQuery()->execute() as $object) {
            if ($this->skip($object)) {
                continue;
            }

            yield $object;
        }
    }

    protected function skip($object): bool
    {
        return false;
    }
}
