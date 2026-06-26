<?php

declare(strict_types=1);

namespace App\Dto\AccordCadre\ListBlocks\Components;

use Symfony\Component\Serializer\Annotation\Groups;

final class ImageItem
{
    #[Groups(['products:get', 'product:get'])]
    private string $imgLink;

    public function getImgLink(): string
    {
        return $this->imgLink;
    }

    public function setImgLink(string $imgLink): self
    {
        $this->imgLink = $imgLink;

        return $this;
    }
}
