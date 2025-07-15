<?php

declare(strict_types=1);

namespace App\Dto;

final class AccordView
{
    public function __construct(
        public string $id,
        public string $name,
        public ?string $logo,
        /** @var AccordStoreView[] */
        public array $stores = [],
    ) {
    }
}
