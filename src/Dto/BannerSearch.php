<?php

declare(strict_types=1);

namespace App\Dto;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use App\State\Provider\BannerSearchProvider;

#[ApiResource(
    operations: [
        new Get(
            uriTemplate: '/banners-search',
        ),
    ],
    provider: BannerSearchProvider::class
)]
class BannerSearch
{
    public const int DYNAMIC_CONFIG_ID = 24;
    #[ApiProperty(identifier: true)]
    private ?int $id = null;

    private string $category = '';
    private string $desktopImg = '';
    private string $mobileImg = '';
    private string $link = '';

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getCategory(): string
    {
        return $this->category;
    }

    public function setCategory(string $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getDesktopImg(): string
    {
        return $this->desktopImg;
    }

    public function setDesktopImg(string $desktopImg): self
    {
        $this->desktopImg = $desktopImg;

        return $this;
    }

    public function getMobileImg(): string
    {
        return $this->mobileImg;
    }

    public function setMobileImg(string $mobileImg): self
    {
        $this->mobileImg = $mobileImg;

        return $this;
    }

    public function getLink(): string
    {
        return $this->link;
    }

    public function setLink(string $link): self
    {
        $this->link = $link;

        return $this;
    }
}
