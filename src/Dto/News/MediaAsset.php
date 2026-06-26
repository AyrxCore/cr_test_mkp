<?php

declare(strict_types=1);

namespace App\Dto\News;

final class MediaAsset
{
    private string $filename = '';

    public function getFilename(): string
    {
        return $this->filename;
    }

    public function setFilename(string $filename): void
    {
        $this->filename = $filename;
    }
}
