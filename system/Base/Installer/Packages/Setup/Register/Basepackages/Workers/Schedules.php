<?php

declare(strict_types=1);

/**
 * SP Framework
 *
 * @package     System\Base\Installer\Packages\Setup\Register\Basepackages\Workers
 * @copyright   Copyright (c) 2026
 * @link        https://github.com/sp-framework/core
 */

namespace System\Base\Installer\Packages\Setup\Register\Basepackages\Workers;

/**
 * Seeds recurring background schedule presets into basepackages_workers_schedules.
 */
class Schedules
{
    /**
     * Registers default background worker cron/interval schedules.
     *
     * @param mixed $db     PDO database connection adapter.
     * @param mixed $ff     FlatFile database manager.
     * @param mixed $helper Helpers service instance.
     *
     * @return void
     */
    public function register(mixed $db, mixed $ff, mixed $helper): void
    {
        $schedulesArr = $this->systemSchedules($helper);

        foreach ($schedulesArr as $schedule) {
            if ($db) {
                $db->insertAsDict('basepackages_workers_schedules', $schedule);
            }

            if ($ff) {
                $scheduleStore = $ff->store('basepackages_workers_schedules');

                $scheduleStore->updateOrInsert($schedule);
            }
        }
    }

    /**
     * Compiles standard system schedule presets.
     *
     * @param mixed $helper Helpers service instance.
     *
     * @return array<int, array<string, mixed>>
     */
    protected function systemSchedules(mixed $helper): array
    {
        $descriptions = [
            'everyxseconds' => 'Task with this schedule will run every X seconds of the minute.',
            'everyminute'   => 'Task with this schedule will run every minute.',
            'everyxminutes' => 'Task with this schedule will run every X minutes from the moment it starts.',
            'hourly'        => 'Task with this schedule will run every hour. If minutes are specified the task will run X minutes past the hour.',
            'daily'         => 'Task with this schedule will run every day. If hour and minutes are specified the task will run daily at X hour.',
            'daily6'        => 'Task with this schedule will run every day every 6th hour.',
            'daily12'       => 'Task with this schedule will run every day every 12th hour.',
            'daily18'       => 'Task with this schedule will run every day every 18th hour.',
            'weekly'        => 'Task with this schedule will run on selected weekday at X hour.',
            'monthly'       => 'Task with this schedule will run in selected month(s) on day X of the month at X hour.',
            'businesshours' => 'Task with this schedule will run every minute during business hours.',
        ];

        $schedulesArr = [];

        // Every 15 Seconds
        $schedulesArr[] = [
            'name'        => 'Every 15 Seconds',
            'description' => $descriptions['everyxseconds'],
            'type'        => 0,
            'schedule'    => $helper->encode(['type' => 'everyxseconds', 'params' => ['seconds' => ['0', '15', '30', '45']]])
        ];

        // Every 30 Seconds
        $schedulesArr[] = [
            'name'        => 'Every 30 Seconds',
            'description' => $descriptions['everyxseconds'],
            'type'        => 0,
            'schedule'    => $helper->encode(['type' => 'everyxseconds', 'params' => ['seconds' => ['0', '30']]])
        ];

        // Every Minute
        $schedulesArr[] = [
            'name'        => 'Every Minute',
            'description' => $descriptions['everyminute'],
            'type'        => 0,
            'schedule'    => $helper->encode(['type' => 'everyminute', 'params' => []])
        ];

        // Every 5 Minutes
        $schedulesArr[] = [
            'name'        => 'Every 5 Minutes',
            'description' => $descriptions['everyxminutes'],
            'type'        => 0,
            'schedule'    => $helper->encode(['type' => 'everyxminutes', 'params' => ['minutes' => 5]])
        ];

        // Hourly
        $schedulesArr[] = [
            'name'        => 'Hourly',
            'description' => $descriptions['hourly'],
            'type'        => 0,
            'schedule'    => $helper->encode(['type' => 'hourly', 'params' => ['minutes' => 0]])
        ];

        // Daily
        $schedulesArr[] = [
            'name'        => 'Daily',
            'description' => $descriptions['daily'],
            'type'        => 0,
            'schedule'    => $helper->encode(['type' => 'daily', 'params' => ['hour' => 0, 'minutes' => 0]])
        ];

        // Daily 6 Hours
        $schedulesArr[] = [
            'name'        => 'Daily Every 6 Hours',
            'description' => $descriptions['daily6'],
            'type'        => 0,
            'schedule'    => $helper->encode(['type' => 'daily6', 'params' => ['minutes' => 0]])
        ];

        // Daily 12 Hours
        $schedulesArr[] = [
            'name'        => 'Daily Every 12 Hours',
            'description' => $descriptions['daily12'],
            'type'        => 0,
            'schedule'    => $helper->encode(['type' => 'daily12', 'params' => ['minutes' => 0]])
        ];

        // Weekly
        $schedulesArr[] = [
            'name'        => 'Weekly',
            'description' => $descriptions['weekly'],
            'type'        => 0,
            'schedule'    => $helper->encode(['type' => 'weekly', 'params' => ['day' => 1, 'hour' => 0, 'minutes' => 0]])
        ];

        // Monthly
        $schedulesArr[] = [
            'name'        => 'Monthly',
            'description' => $descriptions['monthly'],
            'type'        => 0,
            'schedule'    => $helper->encode(['type' => 'monthly', 'params' => ['day' => 1, 'hour' => 0, 'minutes' => 0]])
        ];

        // Business Hours
        $schedulesArr[] = [
            'name'        => 'Business Hours',
            'description' => $descriptions['businesshours'],
            'type'        => 0,
            'schedule'    => $helper->encode(['type' => 'businesshours', 'params' => []])
        ];

        return $schedulesArr;
    }
}