<?php

declare(strict_types=1);

namespace App\Dto\AccordCadre\ListBlocks;

use App\Dto\AccordCadre\AccordCadreContentBlockInterface;
use Symfony\Component\Serializer\Annotation\Groups;

final class BannerBlockContent implements AccordCadreContentBlockInterface
{
    #[Groups(['products:get', 'product:get'])]
    private string $logoUrl;
    #[Groups(['product:get'])]
    private string $componentName;
    #[Groups(['product:get'])]
    private ?string $badgeTextTop = null;
    #[Groups(['products:get', 'product:get'])]
    private string $badgeTextBottom;
    #[Groups(['product:get'])]
    private string $imgBannerUrlDesktop;
    #[Groups(['product:get'])]
    private string $imgBannerUrlMobile;

    public function getLogoUrl(): string
    {
        return $this->logoUrl;
    }

    public function setLogoUrl(string $logoUrl): self
    {
        $this->logoUrl = $logoUrl;

        return $this;
    }

    public function getComponentName(): string
    {
        return $this->componentName;
    }

    public function setComponentName(string $componentName): self
    {
        $this->componentName = $componentName;

        return $this;
    }

    public function getBadgeTextTop(): ?string
    {
        return $this->badgeTextTop;
    }

    public function setBadgeTextTop(?string $badgeTextTop): self
    {
        $this->badgeTextTop = $badgeTextTop;

        return $this;
    }

    public function getBadgeTextBottom(): string
    {
        return $this->badgeTextBottom;
    }

    public function setBadgeTextBottom(string $badgeTextBottom): self
    {
        $this->badgeTextBottom = $badgeTextBottom;

        return $this;
    }

    public function getImgBannerUrlDesktop(): string
    {
        return $this->imgBannerUrlDesktop;
    }

    public function setImgBannerUrlDesktop(string $imgBannerUrlDesktop): self
    {
        $this->imgBannerUrlDesktop = $imgBannerUrlDesktop;

        return $this;
    }

    public function getImgBannerUrlMobile(): string
    {
        return $this->imgBannerUrlMobile;
    }

    public function setImgBannerUrlMobile(string $imgBannerUrlMobile): self
    {
        $this->imgBannerUrlMobile = $imgBannerUrlMobile;

        return $this;
    }
}
