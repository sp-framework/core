<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages;

use GO\Scheduler;
use GO\Traits\Interval;
use Phalcon\Helper\Arr;
use Phalcon\Helper\Json;
use System\Base\BasePackage;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Workers\Jobs;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Workers\Schedules;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Workers\Tasks;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Workers\Workers as WorkersWorkers;

class Workers extends BasePackage
{
    use Interval;

    public $schedulesPackage;

    public $tasksPackage;

    public $tasks;

    public $jobsPackage;

    public $jobs;

    protected $worker;

    protected $schedulerSettings = [];

    protected $dayOfWeek;

    protected $dateOfMonth;

    protected $month;

    public function init(bool $resetCache = false)
    {
        $this->schedulesPackage = (new Schedules())->init();

        $this->tasksPackage = (new Tasks())->init();

        $this->jobsPackage = (new Jobs())->init();

        $workers = (new WorkersWorkers())->init()->getIdleWorkers();
        if (count($workers) > 0) {
            $this->worker = $workers[0];
        } else {
            $this->logger->log->alert('No Workers available at ' . date('Y-m-d H:i:s'));

            return false;//We quite as there are no available workers.
        }

        if ($this->checkTempPath()) {
            $this->schedulerSettings['tempDir'] = base_path('var/workers/');
        }

        $this->worker['scheduler'] = new Scheduler($this->schedulerSettings);

        $this->scheduler = $this->worker['scheduler'];

        $this->dayOfWeek = date('w');

        $this->dateOfMonth = date('j');

        $this->month = date('n');

        return $this;
    }

    public function run()
    {
        $enabledTasks = $this->tasksPackage->getEnabledTasks();

        $availableFunctions = array_keys($this->tasksPackage->getAllFunctions());

        foreach ($enabledTasks as $taskKey => $task) {
            if (in_array($task['function'], $availableFunctions)) {
                $schedule = $this->schedulesPackage->getSchedulesSchedule($task['schedule_id']);

                $class = 'System\\Base\\Providers\\BasepackagesServiceProvider\\Packages\\Workers\\Functions\\' . ucfirst($task['function']);
                if ($schedule['type'] === 'everyminute') {
                    $this->scheduleEveryMinute($task, $schedule, $class);
                } else if ($schedule['type'] === 'everyxminutes') {
                    $this->scheduleEveryXMinute($task, $schedule, $class);
                } else if ($schedule['type'] === 'hourly') {
                    $this->scheduleHourly($task, $schedule, $class);
                } else if ($schedule['type'] === 'daily') {
                    $this->scheduleDaily($task, $schedule, $class);
                } else if ($schedule['type'] === 'weekly') {
                    $this->scheduleWeekly($task, $schedule, $class);
                } else if ($schedule['type'] === 'monthly') {
                    $this->scheduleMonthly($task, $schedule, $class);
                }
            } else {
                $task['enabled'] = 0;
                $task['status'] = 3;
                $task['result'] = 'Task function not found!';

                $this->tasksPackage->update($task);
            }


            // var_dump($this->scheduler->getVerboseOutput());
            // var_dump($this->scheduler->getExecutedJobs());
        }
        var_dump($this->scheduler);
        // $this->scheduler->run();
        //This method will be called via CRON every minute.
        //Taskes that have next_run > current Unix Time will be executed as per their priority.
        //Priorities are set as numbers from 1-10 with 1 being the lowest priority and 10 being the highest.
        //Taskes can be registered to run at certain intervals.
        //Example: Run Email Queue with High priority - Priority set to 10 and process run every minute.
        // $logger = $this->logger;
    }

    protected function scheduleEveryMinute($task, $schedule, $class)
    {
        $nextRun = $this->everyminute()->executionTime->getNextRunDate()->format('Y-m-d H:i:s');

        if ($task['next_run'] !== $nextRun) {
            $task['next_run'] = $nextRun;
            $this->tasksPackage->update($task);
        }

        $this->scheduler->call(
            (new $class)->run(),
            [],
            $task['id'] . '-' . $schedule['type']
        )->everyminute();

        $this->jobsPackage->addJob(
            [
                'task_id'       => $task['id'],
                'worker_id'     => $this->worker['id'],
                'run_on'        => $nextRun,
                'status'        => 0
            ]
        );
    }

    protected function scheduleEveryXMinute($task, $schedule, $class)
    {
        if ($this->shouldSchedule($task, $schedule)) {
            $this->scheduler->call(
                (new $class)->run(),
                [],
                $task['id'] . '-' . $schedule['type']
            )->everyminute(
                (int) $schedule['params']['minutes']
            );
        }
    }

    protected function scheduleHourly($task, $schedule, $class)
    {
        if ($this->shouldSchedule($task, $schedule)) {
            $this->scheduler->call(
                (new $class)->run(),
                [],
                $task['id'] . '-' . $schedule['type']
            )->hourly(
                (int) $schedule['params']['hourly_minutes']
            );
        }
    }

    protected function scheduleDaily($task, $schedule, $class)
    {
        if ($this->shouldSchedule($task, $schedule)) {
            $this->scheduler->call(
                (new $class)->run(),
                [],
                $task['id'] . '-' . $schedule['type']
            )->daily(
                (int) $schedule['params']['daily_hours'],
                (int) $schedule['params']['daily_minutes']
            );
        }
    }

    protected function scheduleWeekly($task, $schedule, $class)
    {
        if (!in_array($this->dayOfWeek, $schedule['params']['weekly_days'])) {
            return false;
        }

        if ($this->shouldSchedule($task, $schedule)) {
            $this->scheduler->call(
                (new $class)->run(),
                [],
                $task['id'] . '-' . $schedule['type'] . '-' . $day
            )->weekly(
                $this->dayOfWeek,
                (int) $schedule['params']['weekly_hours'],
                (int) $schedule['params']['weekly_minutes']
            );
        }
    }

    protected function scheduleMonthly($task, $schedule, $class)
    {
        if (!in_array($this->month, $schedule['params']['monthly_months'])) {
            return false;
        }

        if ($this->dateOfMonth != $schedule['params']['monthly_day']) {
            return false;
        }

        if ($this->shouldSchedule($task, $schedule)) {
            $this->scheduler->call(
                (new $class)->run(),
                [],
                $task['id'] . '-' . $schedule['type'] . '-' . $month
            )->monthly(
                (int) $this->month,
                (int) $this->dateOfMonth,
                (int) $schedule['params']['monthly_hours'],
                (int) $schedule['params']['monthly_minutes']
            );
        }
    }

    //Only Schedule if less than 2 minutes
    protected function shouldSchedule($task, $schedule)
    {
        if ($schedule['type'] === 'everyxminutes') {
            $cron =
                $this->everyminute(
                    (int) $schedule['params']['minutes']
                )->executionTime;

            $secsLeft = $cron->getNextRunDate()->getTimestamp() - time();
            $nextRun = $cron->getNextRunDate()->format('Y-m-d H:i:s');
        } else if ($schedule['type'] === 'hourly') {
            $cron =
                $this->hourly(
                    (int) $schedule['params']['hourly_minutes']
                )->executionTime;

            $secsLeft = $cron->getNextRunDate()->getTimestamp() - time();
            $nextRun = $cron->getNextRunDate()->format('Y-m-d H:i:s');
        } else if ($schedule['type'] === 'daily') {
            $cron =
                $this->daily(
                    (int) $schedule['params']['daily_hours'],
                    (int) $schedule['params']['daily_minutes']
                )->executionTime;

            $secsLeft = $cron->getNextRunDate()->getTimestamp() - time();
            $nextRun = $cron->getNextRunDate()->format('Y-m-d H:i:s');

            if ($task['next_run'] !== $cron->getNextRunDate()) {
                $this->tasksPackage->update($task);
            }
        } else if ($schedule['type'] === 'weekly') {
            $cron =
                $this->weekly(
                    $this->dayOfWeek,
                    (int) $schedule['params']['weekly_hours'],
                    (int) $schedule['params']['weekly_minutes']
                )->executionTime;

            $secsLeft = $cron->getNextRunDate()->getTimestamp() - time();

            $nextRun = $cron->getNextRunDate()->format('Y-m-d H:i:s');

            if ($secsLeft > 43200) {//gt 12 hrs means the time has passed so get next time.
                if (count($schedule['params']['weekly_days']) > 1) {
                    if ($this->dayOfWeek == Arr::last($schedule['params']['weekly_days'])) {//If Saturday, the next day of execution will be 1st of array.
                        $cron =
                            $this->weekly(
                                (int) Arr::first($schedule['params']['weekly_days']),
                                (int) $schedule['params']['weekly_hours'],
                                (int) $schedule['params']['weekly_minutes']
                            )->executionTime;

                        $nextRun = $cron->getNextRunDate()->format('Y-m-d H:i:s');
                    } else {
                        var_dump((int) Arr::last($schedule['params']['weekly_days']));
                        $dayOfWeekKey = array_search($this->dayOfWeek, $schedule['params']['weekly_days']);

                        $nextKey = prefix_get_next_key_array($schedule['params']['weekly_days'], $dayOfWeekKey);

                        $cron =
                            $this->weekly(
                                (int) $schedule['params']['weekly_days'][$nextKey],
                                (int) $schedule['params']['weekly_hours'],
                                (int) $schedule['params']['weekly_minutes']
                            )->executionTime;

                        $nextRun = $cron->getNextRunDate()->format('Y-m-d H:i:s');
                    }
                }
            }
        } else if ($schedule['type'] === 'monthly') {
            $cron =
                $this->monthly(
                    (int) $this->month,
                    (int) $this->dateOfMonth,
                    (int) $schedule['params']['monthly_hours'],
                    (int) $schedule['params']['monthly_minutes']
                )->executionTime;

            $secsLeft = $cron->getNextRunDate()->getTimestamp() - time();

            $nextRun = $cron->getNextRunDate()->format('Y-m-d H:i:s');

            if ($secsLeft > 86400) { //gt 1 day means the time has passed so get next time.
                if (count($schedule['params']['monthly_months']) > 1) {
                    if ($this->month == Arr::last($schedule['params']['monthly_months'])) {//If December, the next day of execution will be 1st of array.
                        $cron =
                            $this->monthly(
                                (int) Arr::first($schedule['params']['monthly_months']),
                                (int) $this->dateOfMonth,
                                (int) $schedule['params']['monthly_hours'],
                                (int) $schedule['params']['monthly_minutes']
                            )->executionTime;

                        $nextRun = $cron->getNextRunDate()->format('Y-m-d H:i:s');
                    } else {
                        $monthKey = array_search($this->month, $schedule['params']['monthly_months']);

                        $nextKey = prefix_get_next_key_array($schedule['params']['monthly_months'], $monthKey);

                        $cron =
                            $this->monthly(
                                (int) $schedule['params']['monthly_months'][$nextKey],
                                (int) $this->dateOfMonth,
                                (int) $schedule['params']['monthly_hours'],
                                (int) $schedule['params']['monthly_minutes']
                            )->executionTime;

                        $nextRun = $cron->getNextRunDate()->format('Y-m-d H:i:s');
                    }
                }
            }
        }

        if ($task['next_run'] !== $nextRun) {
            $task['next_run'] = $nextRun;
            $this->tasksPackage->update($task);
        }

        if ($secsLeft < 120) {
            if ($task['status'] != 1) {
                $task['status'] = 1;
                $this->tasksPackage->update($task);
            }

            return $nextRun;
        }

        return false;
    }

    protected function checkTempPath()
    {
        if (!is_dir(base_path('var/workers/'))) {
            if (!mkdir(base_path('var/workers/'), 0777, true)) {
                return false;
            }
        }

        return true;
    }
}