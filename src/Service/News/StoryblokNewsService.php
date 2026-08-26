<?php

declare(strict_types=1);

namespace App\Service\News;

use App\Dto\News\News;
use App\Enum\Storyblok\StoryblokEndpoint;
use App\Service\Storyblok\StoryblokHttpClient;
use Symfony\Component\DependencyInjection\Attribute\AsAlias;

#[AsAlias(NewsSourceInterface::class)]
final class StoryblokNewsService implements NewsSourceInterface
{
    public function __construct(
        private readonly StoryblokHttpClient $httpClient,
        private readonly StoryblokToNewsMapper $mapper,
    ) {
    }

    /**
     * @return News[]
     */
    public function getAll(): array
    {
        // Récupère les données brutes depuis Storyblok
        $rawData = $this->httpClient->getStories([
            'starts_with' => StoryblokEndpoint::NEWS->value,
            'sort_by' => 'first_published_at:desc',
        ]);

        // Convertit en DTO génériques News
        $newsList = $this->mapper->mapNewsList($rawData);

        // Filtre les stories sans contenu et retourne les DTO directement
        return \array_values(\array_filter(
            $newsList,
            static fn (News $news) => $news->getArticleContent() !== null
        ));
    }
}
