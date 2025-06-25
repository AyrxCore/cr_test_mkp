<?php

declare(strict_types=1);

namespace App\Dto;

use Symfony\Component\Serializer\Annotation\Groups;

readonly class MapStoreDataDto
{
    public function __construct(
        #[Groups(['map:read'])]
        public array $stores = [],

        #[Groups(['map:read'])]
        public array $categories = [],
    ) {
    }
}
