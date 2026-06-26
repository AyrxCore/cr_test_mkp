<?php

declare(strict_types=1);

namespace App\Service\News;

use App\Dto\News\News;

interface NewsSourceInterface
{
    /**
     * Récupère toutes les actualités depuis cette source.
     *
     * @return News[]
     */
    public function getAll(): array;
}
