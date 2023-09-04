<?php

declare(strict_types=1);

namespace App\Dto;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;

#[ApiResource(
    collectionOperations: [],
    itemOperations: [
        'get' => [
            'path' => '/banners/{id}',
            'requirements' => ['id' => '\d+'],
        ],
    ],
)]
class Banner
{
    public const DYNAMIC_CONFIG_ID = 2;
    #[ApiProperty(identifier: true)]
    private ?int $id = null;

    private string $text = '';
    private string $slug = '';
    private string $ctaTxt = '';
    private string $ctaLink = '';

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function setText(string $text): void
    {
        $this->text = $text;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): void
    {
        $this->slug = $slug;
    }

    public function getCtaTxt(): string
    {
        return $this->ctaTxt;
    }

    public function setCtaTxt(string $ctaTxt): void
    {
        $this->ctaTxt = $ctaTxt;
    }

    public function getCtaLink(): string
    {
        return $this->ctaLink;
    }

    public function setCtaLink(string $ctaLink): void
    {
        $this->ctaLink = $ctaLink;
    }
}
