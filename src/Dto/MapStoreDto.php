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
        public ?string $name = null,

        #[Groups(['map:read'])]
        public ?string $address = null,

        #[Groups(['map:read'])]
        public ?string $phone = null,

        #[Groups(['map:read'])]
        public ?string $latitude = null,

        #[Groups(['map:read'])]
        public ?string $longitude = null,

        #[Groups(['map:read'])]
        public ?int $upplerId = null,

        #[Groups(['map:read'])]
        public ?string $partnerName = null,

        #[Groups(['map:read'])]
        public ?string $partnerLogo = null,
    ) {
    }
}
