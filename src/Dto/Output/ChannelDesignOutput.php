<?php

declare(strict_types=1);

namespace App\Dto\Output;

use Symfony\Component\Serializer\Annotation\Groups;

final class ChannelDesignOutput
{
    #[Groups(['channel:get'])]
    public ?string $primaryColor = null;

    #[Groups(['channel:get'])]
    public ?string $secondaryColor = null;

    #[Groups(['channel:get'])]
    public ?string $textColor = null;

    #[Groups(['channel:get'])]
    public ?string $logo = null;

    #[Groups(['channel:get'])]
    public ?string $favicon = null;

    #[Groups(['channel:get'])]
    public ?string $banner = null;

    #[Groups(['channel:get'])]
    public ?string $bannerTitle = null;
}
