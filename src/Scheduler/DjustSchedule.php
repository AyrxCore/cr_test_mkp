<?php

declare(strict_types=1);

namespace App\Scheduler;

use App\Message\Djust\SyncDjustOrdersStateMessage;
use Symfony\Component\Scheduler\Attribute\AsSchedule;
use Symfony\Component\Scheduler\RecurringMessage;
use Symfony\Component\Scheduler\Schedule;
use Symfony\Component\Scheduler\ScheduleProviderInterface;

#[AsSchedule('default')]
final class DjustSchedule implements ScheduleProviderInterface
{
    public function getSchedule(): Schedule
    {
        $schedule = new Schedule();

        // Only sync in production (analytics are not consumed in real-time)
        // APP_MODE is a Docker server variable
        if ('prod' === ($_SERVER['APP_MODE'] ?? null)) {
            $schedule->add(
                RecurringMessage::cron('0 0 * * *', new SyncDjustOrdersStateMessage()),
            );
        }

        return $schedule;
    }
}
