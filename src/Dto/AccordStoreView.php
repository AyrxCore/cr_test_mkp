<?php

declare(strict_types=1);

namespace App\Dto;

final class AccordStoreView
{
    public function __construct(
        public string $id,
        public string $name,
        public string $address,
        public string $latitude,
        public string $longitude,
        public ?string $phone,
    ) {
    }
}
