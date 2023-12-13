<?php

declare(strict_types=1);

namespace App\DataProvider;

use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectRepository;

abstract class AbstractDataProvider implements RestrictedDataProviderInterface
{
    public function __construct(protected ManagerRegistry $managerRegistry)
    {
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return $this->getRessourceClass() === $resourceClass;
    }

    protected function getRepository(): ObjectRepository
    {
        $manager = $this->managerRegistry->getManagerForClass($this->getRessourceClass());

        return $manager->getRepository($this->getRessourceClass());
    }

    protected function createQueryBuilder(string $alias = 'o'): QueryBuilder
    {
        return $this->getRepository()->createQueryBuilder($alias);
    }

    abstract protected function getRessourceClass(): string;
}
