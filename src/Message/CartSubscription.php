<?php

declare(strict_types=1);

namespace App\Message;

use App\Entity\Channel;

class CartSubscription
{
    public function __construct(
        private ?array $productsIds = [],
        private ?string $accountId = null,
        private ?Channel $channel = null,
    ) {
    }

    public function getChannel(): ?Channel
    {
        return $this->channel;
    }

    public function getAccountId(): ?string
    {
        return $this->accountId;
    }

    public function getProductsIds(): ?array
    {
        return $this->productsIds;
    }
}
