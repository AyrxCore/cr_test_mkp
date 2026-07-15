<?php

declare(strict_types=1);

namespace App\Dto;

use Symfony\Component\Serializer\Annotation\Groups;

readonly class StoreDetailDto
{
    public function __construct(
        #[Groups(['store:detail'])]
        public ?string $id = null,
        #[Groups(['store:detail'])]
        public ?string $name = null,
        #[Groups(['store:detail'])]
        public ?string $address = null,
        #[Groups(['store:detail'])]
        public ?string $phone = null,
        #[Groups(['store:detail'])]
        public ?string $latitude = null,
        #[Groups(['store:detail'])]
        public ?string $longitude = null,
        #[Groups(['store:detail'])]
        public ?string $djustId = null,
        #[Groups(['store:detail'])]
        public ?string $partnerName = null,
        #[Groups(['store:detail'])]
        public ?string $logo = null,
        #[Groups(['store:detail'])]
        public array $accordLogos = [],
    ) {
    }
}
