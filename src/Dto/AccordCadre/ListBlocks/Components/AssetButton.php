<?php

declare(strict_types=1);

namespace App\Dto\AccordCadre\ListBlocks\Components;

use Symfony\Component\Serializer\Annotation\Groups;

final class AssetButton
{
    #[Groups(['products:get', 'product:get'])]
    private string $buttonLabel;
    #[Groups(['products:get', 'product:get'])]
    private string $assetLink;

    public function getButtonLabel(): string
    {
        return $this->buttonLabel;
    }

    public function setButtonLabel(string $buttonLabel): self
    {
        $this->buttonLabel = $buttonLabel;

        return $this;
    }

    public function getAssetLink(): string
    {
        return $this->assetLink;
    }

    public function setAssetLink(string $assetLink): self
    {
        $this->assetLink = $assetLink;

        return $this;
    }
}
