<?php

declare(strict_types=1);

namespace App\Dto\News;

use App\Filter\FilterableInterface;

final class News implements FilterableInterface
{
    private string $slug = '';

    private ?string $fullSlug = null;

    private ?string $firstPublishedAt = null;

    /** @var array<string> */
    private array $tagsList = [];

    private ?string $articleTitle = null;
    private ?string $categoryName = null;
    private ?string $categoryColor = null;
    private ?string $articleContent = null;
    private ?string $ctaTxt = null;
    private ?string $ctaLink = null;
    private bool $displayBanner = false;

    private ?MediaAsset $articleImgMobile = null;

    private ?MediaAsset $articleImgDesktop = null;

    private ?MediaAsset $bannerImgMobile = null;

    private ?MediaAsset $bannerImgDesktop = null;

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): void
    {
        $this->slug = $slug;
    }

    public function getFullSlug(): ?string
    {
        return $this->fullSlug;
    }

    public function setFullSlug(?string $fullSlug): void
    {
        $this->fullSlug = $fullSlug;
    }

    public function getFirstPublishedAt(): ?string
    {
        return $this->firstPublishedAt;
    }

    public function setFirstPublishedAt(?string $firstPublishedAt): void
    {
        $this->firstPublishedAt = $firstPublishedAt;
    }

    /**
     * @return array<string>
     */
    public function getTagsList(): array
    {
        return $this->tagsList;
    }

    /**
     * @param array<string> $tagsList
     */
    public function setTagsList(array $tagsList): void
    {
        $this->tagsList = $tagsList;
    }

    public function getFilterCriteria(): array
    {
        return [
            'tags' => \array_map(static fn ($tag) => \strtolower(\trim($tag)), $this->tagsList),
        ];
    }

    public function getArticleTitle(): ?string
    {
        return $this->articleTitle;
    }

    public function setArticleTitle(?string $articleTitle): void
    {
        $this->articleTitle = $articleTitle;
    }

    public function getCategoryName(): ?string
    {
        return $this->categoryName;
    }

    public function setCategoryName(?string $categoryName): void
    {
        $this->categoryName = $categoryName;
    }

    public function getCategoryColor(): ?string
    {
        return $this->categoryColor;
    }

    public function setCategoryColor(?string $categoryColor): void
    {
        $this->categoryColor = $categoryColor;
    }

    public function getArticleContent(): ?string
    {
        return $this->articleContent;
    }

    public function setArticleContent(?string $articleContent): void
    {
        $this->articleContent = $articleContent;
    }

    public function getCtaTxt(): ?string
    {
        return $this->ctaTxt;
    }

    public function setCtaTxt(?string $ctaTxt): void
    {
        $this->ctaTxt = $ctaTxt;
    }

    public function getCtaLink(): ?string
    {
        return $this->ctaLink;
    }

    public function setCtaLink(?string $ctaLink): void
    {
        $this->ctaLink = $ctaLink;
    }

    public function getDisplayBanner(): bool
    {
        return $this->displayBanner;
    }

    public function setDisplayBanner(bool $displayBanner): void
    {
        $this->displayBanner = $displayBanner;
    }

    public function getArticleImgMobile(): ?MediaAsset
    {
        return $this->articleImgMobile;
    }

    public function setArticleImgMobile(?MediaAsset $articleImgMobile): void
    {
        $this->articleImgMobile = $articleImgMobile;
    }

    public function getArticleImgDesktop(): ?MediaAsset
    {
        return $this->articleImgDesktop;
    }

    public function setArticleImgDesktop(?MediaAsset $articleImgDesktop): void
    {
        $this->articleImgDesktop = $articleImgDesktop;
    }

    public function getBannerImgMobile(): ?MediaAsset
    {
        return $this->bannerImgMobile;
    }

    public function setBannerImgMobile(?MediaAsset $bannerImgMobile): void
    {
        $this->bannerImgMobile = $bannerImgMobile;
    }

    public function getBannerImgDesktop(): ?MediaAsset
    {
        return $this->bannerImgDesktop;
    }

    public function setBannerImgDesktop(?MediaAsset $bannerImgDesktop): void
    {
        $this->bannerImgDesktop = $bannerImgDesktop;
    }
}
