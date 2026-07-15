<?php

declare(strict_types=1);

namespace App\Dto;

use Symfony\Component\Serializer\Annotation\Groups;

readonly class MapStoreDto
{
    public function __construct(
        #[Groups(['map:read'])]
        public ?string $id = null,
        #[Groups(['map:read'])]
        public ?string $latitude = null,
        #[Groups(['map:read'])]
        public ?string $longitude = null,
    ) {
    }
}
