<?php

declare(strict_types=1);

namespace App\Dto;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;

#[ApiResource(
    collectionOperations: [],
    itemOperations: [
        'get' => [
            'path'         => '/banner/{id}',
            'requirements' => ['id' => '\d+'],
        ],
    ],
    formats: ['json'],
)]
class ExpertContentBanner
{

    #[ApiProperty(identifier: true)]
    private ?int $id = null;

    private string $text = '';
    private string $slug = '';
    private string $ctaTxt = '';
    private string $ctaLink = '';

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int|null $id
     */
    public function setId(?int $id): void
    {
        $this->id = $id;
    }


    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * @param string $text
     */
    public function setText(string $text): void
    {
        $this->text = $text;
    }

    /**
     * @return string
     */
    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * @param string $slug
     */
    public function setSlug(string $slug): void
    {
        $this->slug = $slug;
    }

    /**
     * @return string
     */
    public function getCtaTxt(): string
    {
        return $this->ctaTxt;
    }

    /**
     * @param string $ctaTxt
     */
    public function setCtaTxt(string $ctaTxt): void
    {
        $this->ctaTxt = $ctaTxt;
    }

    /**
     * @return string
     */
    public function getCtaLink(): string
    {
        return $this->ctaLink;
    }

    /**
     * @param string $ctaLink
     */
    public function setCtaLink(string $ctaLink): void
    {
        $this->ctaLink = $ctaLink;
    }

}

