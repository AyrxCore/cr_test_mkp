<?php

declare(strict_types=1);

namespace App\Filter;

use App\Service\Djust\DjustCustomerAccountService;

class UserTagsFilter implements DtoFilterInterface
{
    public function __construct(
        private readonly DjustCustomerAccountService $djustCustomerAccountService,
    ) {
    }

    public function shouldInclude(FilterableInterface $object): bool
    {
        $criteria = $object->getFilterCriteria();
        $requiredTags = $criteria['tags'] ?? [];

        if (empty($requiredTags)) {
            return false;
        }

        $userTags = \array_map(
            fn ($tag) => \strtolower(\trim($tag)),
            $this->djustCustomerAccountService->getUserTags()
        );

        if (empty($userTags)) {
            return false;
        }

        return !empty(\array_intersect($requiredTags, $userTags));
    }

    public function getName(): string
    {
        return 'user_tags_filter';
    }
}
