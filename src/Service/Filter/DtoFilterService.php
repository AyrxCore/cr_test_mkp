<?php

declare(strict_types=1);

namespace App\Service\Filter;

use App\Filter\DtoFilterInterface;
use App\Filter\FilterableInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;

class DtoFilterService
{
    /**
     * @param DtoFilterInterface[] $filters
     */
    public function __construct(
        #[TaggedIterator('app.dto_filter')]
        private readonly iterable $filters,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function filter(array $objects): array
    {
        if (empty($objects)) {
            return [];
        }

        $filteredObjects = [];

        foreach ($objects as $object) {
            if (!$object instanceof FilterableInterface) {
                $this->logger->warning('Object does not implement FilterableInterface', [
                    'object_class' => $object::class,
                ]);
                continue;
            }

            if ($this->shouldInclude($object)) {
                $filteredObjects[] = $object;
            }
        }

        return $filteredObjects;
    }

    public function shouldInclude(FilterableInterface $object): bool
    {
        foreach ($this->filters as $filter) {
            if (!$filter->shouldInclude($object)) {
                return false;
            }
        }

        return true;
    }
}
