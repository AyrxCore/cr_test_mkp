<?php

declare(strict_types=1);

namespace App\Dto\Output;

use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;

final class ChannelOutput
{
    #[Groups(['channel:get'])]
    public Uuid $id;

    #[Groups(['channel:get'])]
    public string $name;

    #[Groups(['channel:get'])]
    public string $code;

    #[Groups(['channel:get'])]
    public string $hostname;

    #[Groups(['channel:get'])]
    public string $email;

    #[Groups(['channel:get'])]
    public string $phoneNumber;

    #[Groups(['channel:get'])]
    public ?bool $whiteLabel = null;

    #[Groups(['channel:get'])]
    public ChannelDesignOutput $design;

    #[Groups(['channel:get'])]
    public ChannelDocumentsOutput $documents;

    #[Groups(['channel:get'])]
    public array $options;
}
