<?php

declare(strict_types=1);

namespace App\Service\News;

use App\Service\Filter\DtoFilterService;

final class NewsService
{
    public function __construct(
        private readonly NewsSourceInterface $newsSource,
        private readonly DtoFilterService $dtoFilterService,
    ) {
    }

    public function getAll(): array
    {
        $news = $this->newsSource->getAll();

        return $this->dtoFilterService->filter($news);
    }
}
