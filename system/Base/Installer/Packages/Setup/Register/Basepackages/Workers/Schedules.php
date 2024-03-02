<?php

namespace System\Base\Installer\Packages\Setup\Register\Basepackages\Workers;

class Schedules
{
    public function register($db, $ff, $helper)
    {
        $schedulesArr = $this->systemSchedules($helper);

        foreach ($schedulesArr as $key => $schedule) {
            if ($db) {
                $db->insertAsDict('basepackages_workers_schedules', $schedule);
            }

            if ($ff) {
                $scheduleStore = $ff->store('basepackages_workers_schedules');

                $scheduleStore->updateOrInsert($schedule);
            }
        }
    }

    protected function systemSchedules($helper)
    {
        $descriptions =
            [
                'everyminute'               => 'Task with this schedule will run every minute.',
                'everyxminutes'             => 'Task with this schedule will run every X minutes from the moment it starts.',
                'hourly'                    => 'Task with this schedule will run every hour. If minutes are specified the task will run X minutes past the hour.',
                'daily'                     => 'Task with this schedule will run every day. If hour and minutes are specified the task will run daily at X hour.',
                'daily6'                    => 'Task with this schedule will run every day every 6th hour.',
                'daily12'                   => 'Task with this schedule will run every day every 12th hour.',
                'daily18'                   => 'Task with this schedule will run every day every 18th hour.',
                'weekly'                    => 'Task with this schedule will run on selected weekday at X hour.',
                'monthly'                   => 'Task with this schedule will run in selected month(s) on day X of the month at X hour.',
                'businesshours'             => 'Task with this schedule will run every minute during business hours.',
            ];

        $schedulesArr = [];

        //EveryMinute
        $schedule =
            [
                'type'      => 'everyminute'
            ];
        $scheduleEntry =
            [
                'name'          => 'Every Minute',
                'description'   => $descriptions['everyminute'],
                'type'          => 0,
                'schedule'      => $helper->encode($schedule)
            ];
        array_push($schedulesArr, $scheduleEntry);

        //Every 15 Minutes
        $schedule =
            [
                'type'      => 'everyxminutes',
                'params'    =>
                    [
                        'minutes'   => '15'
                    ]
            ];
        $scheduleEntry =
            [
                'name'          => 'Every 15 Minutes',
                'description'   => $descriptions['everyxminutes'],
                'type'          => 0,
                'schedule'      => $helper->encode($schedule)
            ];
        array_push($schedulesArr, $scheduleEntry);

        //Every 30 Minutes
        $schedule =
            [
                'type'      => 'everyxminutes',
                'params'    =>
                    [
                        'minutes'   => '30'
                    ]
            ];
        $scheduleEntry =
            [
                'name'          => 'Every 30 Minutes',
                'description'   => $descriptions['everyxminutes'],
                'type'          => 0,
                'schedule'      => $helper->encode($schedule)
            ];
        array_push($schedulesArr, $scheduleEntry);

        //Business Hours 8AM to 5PM
        $schedule =
            [
                'type'      => 'everyxminutesbetween',
                'params'    =>
                    [
                        'minutes'   => '1',
                        'start'     => '08:00',
                        'end'       => '17:00'
                    ]
            ];
        $scheduleEntry =
            [
                'name'          => 'Business hours (Every Minute 08:00 - 17:00)',
                'description'   => $descriptions['businesshours'],
                'type'          => 0,
                'schedule'      => $helper->encode($schedule)
            ];
        array_push($schedulesArr, $scheduleEntry);

        //Every Hour
        $schedule =
            [
                'type'      => 'hourly',
                'params'    =>
                    [
                        'hourly_minutes'   => '00'
                    ]
            ];
        $scheduleEntry =
            [
                'name'          => 'Every Hour',
                'description'   => $descriptions['hourly'],
                'type'          => 0,
                'schedule'      => $helper->encode($schedule)
            ];
        array_push($schedulesArr, $scheduleEntry);

        //Everyday (midnight)
        $schedule =
            [
                'type'      => 'daily',
                'params'    =>
                    [
                        'daily_hours'     => '00',
                        'daily_minutes'   => '00'
                    ]
            ];
        $scheduleEntry =
            [
                'name'          => 'Everyday',
                'description'   => $descriptions['daily'],
                'type'          => 0,
                'schedule'      => $helper->encode($schedule)
            ];
        array_push($schedulesArr, $scheduleEntry);

        //Everyday (6th hour)
        $schedule =
            [
                'type'      => 'daily',
                'params'    =>
                    [
                        'daily_hours'     => '06',
                        'daily_minutes'   => '00'
                    ]
            ];
        $scheduleEntry =
            [
                'name'          => 'Everyday 6th Hour',
                'description'   => $descriptions['daily6'],
                'type'          => 0,
                'schedule'      => $helper->encode($schedule)
            ];
        array_push($schedulesArr, $scheduleEntry);

        //Everyday (12th hour)
        $schedule =
            [
                'type'      => 'daily',
                'params'    =>
                    [
                        'daily_hours'     => '12',
                        'daily_minutes'   => '00'
                    ]
            ];
        $scheduleEntry =
            [
                'name'          => 'Everyday 12th Hour',
                'description'   => $descriptions['daily12'],
                'type'          => 0,
                'schedule'      => $helper->encode($schedule)
            ];
        array_push($schedulesArr, $scheduleEntry);

        //Everyday (18th hour)
        $schedule =
            [
                'type'      => 'daily',
                'params'    =>
                    [
                        'daily_hours'     => '18',
                        'daily_minutes'   => '00'
                    ]
            ];
        $scheduleEntry =
            [
                'name'          => 'Everyday 18th Hour',
                'description'   => $descriptions['daily18'],
                'type'          => 0,
                'schedule'      => $helper->encode($schedule)
            ];
        array_push($schedulesArr, $scheduleEntry);

        //Everymonth on day 1
        $schedule =
            [
                'type'      => 'monthly',
                'params'    =>
                    [
                        'monthly_months'        => ['1','2','3','4','5','6','7','8','9','10','11','12'],
                        'monthly_day'           => '1',
                        'monthly_hours'         => '00',
                        'monthly_minutes'       => '00'
                    ]
            ];
        $scheduleEntry =
            [
                'name'          => 'Every Month (Day 1)',
                'description'   => $descriptions['monthly'],
                'type'          => 0,
                'schedule'      => $helper->encode($schedule)
            ];
        array_push($schedulesArr, $scheduleEntry);

        return $schedulesArr;
    }
}