<?php

declare(strict_types=1);

namespace App\Service\News;

use App\Dto\News\MediaAsset;
use App\Dto\News\News;
use App\Service\Storyblok\StoryblokRichTextResolver;
use Psr\Log\LoggerInterface;

final class StoryblokToNewsMapper
{
    // Constantes pour les clés Storyblok
    private const string KEY_STORIES = 'stories';
    private const string KEY_CONTENT = 'content';
    private const string KEY_TAGS = 'tags';
    private const string KEY_SLUG = 'slug';
    private const string KEY_FULL_SLUG = 'full_slug';
    private const string KEY_FIRST_PUBLISHED_AT = 'first_published_at';

    // Constantes pour les clés du contenu
    private const string KEY_ARTICLE_TITLE = 'articleTitle';
    private const string KEY_CATEGORY_NAME = 'categoryName';
    private const string KEY_CATEGORY_COLOR = 'categoryColor';
    private const string KEY_ARTICLE_CONTENT = 'articleContent';
    private const string KEY_CTA_TXT = 'ctaTxt';
    private const string KEY_CTA_LINK = 'ctaLink';
    private const string KEY_DISPLAY_BANNER = 'displayBanner';
    private const string KEY_DISPLAY_BANNER_ALT = 'display_banner';
    private const string KEY_ARTICLE_IMG_MOBILE = 'articleImgMobile';
    private const string KEY_ARTICLE_IMG_DESKTOP = 'articleImgDesktop';
    private const string KEY_BANNER_IMG_MOBILE = 'bannerImgMobile';
    private const string KEY_BANNER_IMG_DESKTOP = 'bannerImgDesktop';
    private const string KEY_FILENAME = 'filename';

    public function __construct(
        private readonly LoggerInterface $storyblokLogger,
        private readonly StoryblokRichTextResolver $richTextResolver,
    ) {
    }

    public function mapNewsList(array $storyblokResponse): array
    {
        return \array_map(
            fn (array $news) => $this->mapNews($news),
            $storyblokResponse[self::KEY_STORIES] ?? []
        );
    }

    public function mapNews(array $storyblokData): News
    {
        $news = new News();
        $news->setSlug($storyblokData[self::KEY_SLUG] ?? '');
        $news->setFullSlug($storyblokData[self::KEY_FULL_SLUG] ?? null);

        // Validation de la date avant stockage
        $dateValue = $storyblokData[self::KEY_FIRST_PUBLISHED_AT] ?? null;
        if ($dateValue !== null) {
            try {
                // Tenter de parser la date pour valider le format
                new \DateTimeImmutable($dateValue);
                $news->setFirstPublishedAt($dateValue);
            } catch (\Exception $e) {
                $this->storyblokLogger->warning('Failed to parse first_published_at date', [
                    'slug' => $storyblokData[self::KEY_SLUG] ?? 'unknown',
                    'date_value' => $dateValue,
                    'exception' => $e->getMessage(),
                ]);
                $news->setFirstPublishedAt(null);
            }
        } else {
            $news->setFirstPublishedAt(null);
        }

        $contentTags = $storyblokData[self::KEY_CONTENT][self::KEY_TAGS] ?? [];
        $tags = \is_array($contentTags) ? $contentTags : [$contentTags];

        $news->setTagsList($tags);

        $contentData = $storyblokData[self::KEY_CONTENT] ?? [];
        $news->setArticleTitle($contentData[self::KEY_ARTICLE_TITLE] ?? null);
        $news->setCategoryName($contentData[self::KEY_CATEGORY_NAME] ?? null);
        $news->setCategoryColor($contentData[self::KEY_CATEGORY_COLOR] ?? null);

        $articleContent = $contentData[self::KEY_ARTICLE_CONTENT] ?? null;
        $renderedHtml = $this->richTextResolver->render($articleContent);
        $news->setArticleContent($renderedHtml !== null ? \html_entity_decode($renderedHtml) : null);

        $news->setCtaTxt($contentData[self::KEY_CTA_TXT] ?? null);
        $news->setCtaLink($contentData[self::KEY_CTA_LINK] ?? null);

        $displayBanner = $contentData[self::KEY_DISPLAY_BANNER] ?? $contentData[self::KEY_DISPLAY_BANNER_ALT] ?? false;
        $news->setDisplayBanner((bool) $displayBanner);

        // Mapping des assets media
        if (isset($contentData[self::KEY_ARTICLE_IMG_MOBILE]) && \is_array($contentData[self::KEY_ARTICLE_IMG_MOBILE])) {
            $news->setArticleImgMobile($this->mapAsset($contentData[self::KEY_ARTICLE_IMG_MOBILE]));
        }

        if (isset($contentData[self::KEY_ARTICLE_IMG_DESKTOP]) && \is_array($contentData[self::KEY_ARTICLE_IMG_DESKTOP])) {
            $news->setArticleImgDesktop($this->mapAsset($contentData[self::KEY_ARTICLE_IMG_DESKTOP]));
        }

        if (isset($contentData[self::KEY_BANNER_IMG_MOBILE]) && \is_array($contentData[self::KEY_BANNER_IMG_MOBILE])) {
            $news->setBannerImgMobile($this->mapAsset($contentData[self::KEY_BANNER_IMG_MOBILE]));
        }

        if (isset($contentData[self::KEY_BANNER_IMG_DESKTOP]) && \is_array($contentData[self::KEY_BANNER_IMG_DESKTOP])) {
            $news->setBannerImgDesktop($this->mapAsset($contentData[self::KEY_BANNER_IMG_DESKTOP]));
        }

        return $news;
    }

    /**
     * Convertit un asset Storyblok en MediaAsset.
     */
    private function mapAsset(array $assetData): MediaAsset
    {
        $asset = new MediaAsset();
        $asset->setFilename($assetData[self::KEY_FILENAME] ?? '');

        return $asset;
    }
}
