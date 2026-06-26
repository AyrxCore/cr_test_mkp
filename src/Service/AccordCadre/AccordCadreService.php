<?php

declare(strict_types=1);

namespace App\Service\AccordCadre;

use App\Dto\AccordCadre\AccordCadreContent;
use App\Enum\Storyblok\StoryblokEndpoint;
use App\Service\Storyblok\StoryblokHttpClient;
use Psr\Cache\CacheItemPoolInterface;

class AccordCadreService
{
    private const string CACHE_KEY = 'storyblok_accord_cadre_content';

    public function __construct(
        private readonly StoryblokHttpClient $httpClient,
        private readonly StoryblokToAccordCadreMapper $mapper,
        private readonly CacheItemPoolInterface $cache,
    ) {
    }

    public function getTarifIds(): array
    {
        $tarifIds = [];
        foreach ($this->getAll() as $content) {
            $tarifId = $content->getTarifId();
            if ($tarifId !== null && $tarifId !== '') {
                $tarifIds[$tarifId] = true;
            }
        }

        return $tarifIds;
    }

    public function getAccordCadreContentByTarifId(string $tarifId): ?AccordCadreContent
    {
        $accordCadreCache = $this->cache->getItem(self::CACHE_KEY);

        if (!$accordCadreCache->isHit()) {
            $accordCadreCache->set($this->getAll());
            $accordCadreCache->expiresAfter(180);
            $this->cache->save($accordCadreCache);
        }

        /** @var AccordCadreContent[] $accordCadreContents */
        $accordCadreContents = $accordCadreCache->get();

        foreach ($accordCadreContents as $accordCadreContent) {
            if ($accordCadreContent->getTarifId() === $tarifId) {
                return $accordCadreContent;
            }
        }

        return null;
    }

    /**
     * @return AccordCadreContent[]
     */
    public function getAll(): array
    {
        $rawData = $this->httpClient->getStories([
            'starts_with' => StoryblokEndpoint::ACCORD_CADRE->value,
            'sort_by' => 'first_published_at:desc',
        ]);
        $accordCadreContentDtos = $this->mapper->mapStories($rawData);

        return \array_values(\array_filter(
            $accordCadreContentDtos,
            fn (AccordCadreContent $story) => \count($story->getListBlocks()) > 0
        ));
    }
}
