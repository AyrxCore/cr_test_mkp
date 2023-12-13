<?php

declare(strict_types=1);

namespace App\Dto\Output;

use Symfony\Component\Serializer\Annotation\Groups;

final class ChannelDocumentsOutput
{
    #[Groups(['channel:get'])]
    public ?string $privacyPolicy = null;

    #[Groups(['channel:get'])]
    public ?string $legalTerms = null;

    #[Groups(['channel:get'])]
    public ?string $generalTermsOfUse = null;
}
