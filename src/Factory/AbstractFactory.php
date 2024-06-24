<?php

declare(strict_types=1);

namespace App\Factory;

use Psr\Cache\CacheItemPoolInterface as AdapterInterface;

abstract class AbstractFactory implements FactoryInterface
{
    public function __construct(protected AdapterInterface $cache)
    {
    }

    abstract public function create(array $data): mixed;

    public function createAndAddToCollection(array $data): array
    {
        $array = [];
        foreach ($data as $remoteData) {
            $array[] = $this->create($remoteData);
        }

        return $array;
    }
}
