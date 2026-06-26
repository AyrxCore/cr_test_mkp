<?php

declare(strict_types=1);

namespace App\Dto\AccordCadre\ListBlocks;

use App\Dto\AccordCadre\AccordCadreContentBlockInterface;
use App\Dto\AccordCadre\ListBlocks\Components\AssetButton;
use App\Dto\AccordCadre\ListBlocks\Components\ImageItem;
use Symfony\Component\Serializer\Annotation\Groups;

final class NegociatedTermsBlockContent implements AccordCadreContentBlockInterface
{
    #[Groups(['product:get'])]
    private string $componentName;
    #[Groups(['product:get'])]
    private string $title;
    #[Groups(['product:get'])]
    private string $description;
    #[Groups(['product:get'])]
    private ?string $detailsTitle = null;
    #[Groups(['product:get'])]
    private ?string $detailsContent = null;
    #[Groups(['product:get'])]
    private ?string $negociatedTermsButtonLabel = null;
    /** @var ImageItem[] */
    #[Groups(['product:get'])]
    private ?array $negociatedTermsLayerItems = [];
    /** @var AssetButton[] */
    #[Groups(['product:get'])]
    private ?array $assetButtons = [];

    public function getComponentName(): string
    {
        return $this->componentName;
    }

    public function setComponentName(string $componentName): self
    {
        $this->componentName = $componentName;

        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getDetailsTitle(): ?string
    {
        return $this->detailsTitle;
    }

    public function setDetailsTitle(?string $detailsTitle): self
    {
        $this->detailsTitle = $detailsTitle;

        return $this;
    }

    public function getDetailsContent(): ?string
    {
        return $this->detailsContent;
    }

    public function setDetailsContent(?string $detailsContent): self
    {
        $this->detailsContent = $detailsContent;

        return $this;
    }

    public function getNegociatedTermsButtonLabel(): ?string
    {
        return $this->negociatedTermsButtonLabel;
    }

    public function setNegociatedTermsButtonLabel(?string $negociatedTermsButtonLabel): self
    {
        $this->negociatedTermsButtonLabel = $negociatedTermsButtonLabel;

        return $this;
    }

    public function getNegociatedTermsLayerItems(): ?array
    {
        return $this->negociatedTermsLayerItems;
    }

    public function setNegociatedTermsLayerItems(?array $negociatedTermsLayerItems): self
    {
        $this->negociatedTermsLayerItems = $negociatedTermsLayerItems;

        return $this;
    }

    public function addNegociatedTermsLayerItem(ImageItem $negociatedTermsLayerItem): self
    {
        $this->negociatedTermsLayerItems[] = $negociatedTermsLayerItem;

        return $this;
    }

    public function getAssetButtons(): ?array
    {
        return $this->assetButtons;
    }

    public function setAssetButtons(?array $assetButtons): self
    {
        $this->assetButtons = $assetButtons;

        return $this;
    }

    public function addAssetButton(AssetButton $assetButton): self
    {
        $this->assetButtons[] = $assetButton;

        return $this;
    }
}
