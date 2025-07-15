<?php

declare(strict_types=1);

namespace App\Enum;

enum UpplerWebhookNotificationTypes: string
{
    case ORDER_STATE_UPDATE = 'ORDER_STATE_UPDATE';
}
