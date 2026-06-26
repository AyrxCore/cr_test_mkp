<?php

declare(strict_types=1);

namespace App\Dto\AccordCadre\ListBlocks;

use App\Dto\AccordCadre\AccordCadreContentBlockInterface;
use App\Dto\AccordCadre\ListBlocks\Components\AssetButton;
use Symfony\Component\Serializer\Annotation\Groups;

final class PresentationBlockContent implements AccordCadreContentBlockInterface
{
    #[Groups(['products:get', 'product:get'])]
    private string $title;
    #[Groups(['product:get'])]
    private ?string $rseScore = null;
    #[Groups(['product:get'])]
    private string $componentName;
    #[Groups(['product:get'])]
    private string $description;
    #[Groups(['product:get'])]
    private ?string $bulletpoints = null;
    #[Groups(['product:get'])]
    private ?string $layerMoreInformationsDescription = null;
    #[Groups(['product:get'])]
    private ?string $layerMoreInformationsPhone = null;
    #[Groups(['product:get'])]
    private ?string $layerMoreInformationsPhoneDescription = null;
    /** @var AssetButton[] */
    #[Groups(['product:get'])]
    private ?array $layerMoreInformationsAssetButtons = [];

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getRseScore(): ?string
    {
        return $this->rseScore;
    }

    public function setRseScore(?string $rseScore): self
    {
        $this->rseScore = $rseScore;

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

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getBulletpoints(): ?string
    {
        return $this->bulletpoints;
    }

    public function setBulletpoints(?string $bulletpoints): self
    {
        $this->bulletpoints = $bulletpoints;

        return $this;
    }

    public function getLayerMoreInformationsDescription(): ?string
    {
        return $this->layerMoreInformationsDescription;
    }

    public function setLayerMoreInformationsDescription(?string $layerMoreInformationsDescription): self
    {
        $this->layerMoreInformationsDescription = $layerMoreInformationsDescription;

        return $this;
    }

    public function getLayerMoreInformationsPhone(): ?string
    {
        return $this->layerMoreInformationsPhone;
    }

    public function setLayerMoreInformationsPhone(?string $layerMoreInformationsPhone): self
    {
        $this->layerMoreInformationsPhone = $layerMoreInformationsPhone;

        return $this;
    }

    public function getLayerMoreInformationsPhoneDescription(): ?string
    {
        return $this->layerMoreInformationsPhoneDescription;
    }

    public function setLayerMoreInformationsPhoneDescription(?string $layerMoreInformationsPhoneDescription): self
    {
        $this->layerMoreInformationsPhoneDescription = $layerMoreInformationsPhoneDescription;

        return $this;
    }

    public function getLayerMoreInformationsAssetButtons(): ?array
    {
        return $this->layerMoreInformationsAssetButtons;
    }

    public function setLayerMoreInformationsAssetButtons(?array $layerMoreInformationsAssetButtons): self
    {
        $this->layerMoreInformationsAssetButtons = $layerMoreInformationsAssetButtons;

        return $this;
    }

    public function addLayerMoreInformationsAssetButton(AssetButton $assetButton): self
    {
        $this->layerMoreInformationsAssetButtons[] = $assetButton;

        return $this;
    }
}
