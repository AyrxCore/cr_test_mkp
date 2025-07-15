<?php

declare(strict_types=1);

namespace App\Message;

class UpplerOrderUpdateNotificationMessage
{
    public function __construct(
        public int $orderId,
    ) {
    }

    public function getOrderId(): int
    {
        return $this->orderId;
    }
}
