<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages;

use Carbon\Carbon;
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

    public $workers;

    public $idleWorkers;

    public $schedules;

    public $scheduler;

    public $tasks;

    public $jobs;

    protected $cron;

    protected $scheduledJobs = [];

    protected $worker;

    protected $schedulerSettings = [];

    protected $dayOfWeek;

    protected $dateOfMonth;

    protected $month;

    public function init(bool $resetCache = false)
    {
        $this->workers = (new WorkersWorkers())->init(true);

        $this->schedules = (new Schedules())->init(true);

        $this->tasks = (new Tasks())->init(true);

        $this->jobs = (new Jobs())->init();

        if ($this->checkTempPath()) {
            $this->schedulerSettings['tempDir'] = base_path('var/workers/');
        }

        $this->scheduler = new Scheduler($this->schedulerSettings);

        $this->dayOfWeek = date('w');

        $this->dateOfMonth = date('j');

        $this->month = date('n');

        return $this;
    }

    public function run()
    {
        // Task Statuses
        // 1 - Scheduled
        // 2 - Running
        // 3 - Error

        // Job Statuses
        // 1 - Scheduled
        // 2 - Running
        // 3 - Success
        // 4 - Error
        // 5 - Warning


        // if (!$this->checkIdleWorkers()) {
        //     $this->logger->log->alert('No Workers available at ' . date('Y-m-d H:i:s'));

        //     return false;//We quite as there are no available workers.
        // }

        $enabledTasks = $this->tasks->getEnabledTasks();

        $availableFunctions = array_keys($this->tasks->getAllFunctions());

        foreach ($enabledTasks as $taskKey => $task) {
            if (in_array($task['function'], $availableFunctions)) {
                $schedule = $this->schedules->getSchedulesSchedule($task['schedule_id']);

                $class = 'System\\Base\\Providers\\BasepackagesServiceProvider\\Packages\\Workers\\Functions\\' . ucfirst($task['function']);

                if ($schedule['type'] === 'everyminute') {
                    $this->scheduleEveryMinute($task, $schedule, $class);
                } else if ($schedule['type'] === 'everyxminutes') {
                    $this->scheduleEveryXMinutes($task, $schedule, $class);
                } else if ($schedule['type'] === 'everyxminutesbetween') {
                    $this->scheduleEveryXMinutesBetween($task, $schedule, $class);
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
                $task['status'] = 3;//Error
                $task['result'] = 'Task function not found!';

                $this->tasks->update($task);
            }
        }
        // var_dump($this->scheduledJobs);
        // die();
        // var_dump($this->worker);
        // var_dump($this->scheduler->getQueuedJobs()[0]->getId());
        // var_dump($this->scheduler);
        // die();
        $this->scheduler->run();
        // var_dump('done');
        $failedJobs = $this->scheduler->getFailedJobs();
        // var_dump($failedJobs);die();
        if (count($failedJobs) > 0) {
            foreach ($failedJobs as $failedJobKey => $failedJob) {
                $id = $failedJob->getJob()->getId();
                $this->scheduledJobs[$id]['status'] = 4;//Error
                $this->scheduledJobs[$id]['result'] = $failedJob->getException()->getMessage();

                $this->jobs->updateJob($this->scheduledJobs[$id]);
            }
        }

        $executedJobs = $this->scheduler->getExecutedJobs();

        if (count($executedJobs) > 0) {
            foreach ($executedJobs as $executedJobKey => $executedJob) {
                $id = $executedJob->getId();
                // var_dump($executedJob->getOutput());
                // $this->scheduledJobs[$id]['status'] = 4;//Error
                // $this->scheduledJobs[$id]['result'] = $failedJob->getException()->getMessage();

                // $this->jobs->updateJob($this->scheduledJobs[$id]);
            }
        }

        $this->releaseWorkers();
    }

    protected function scheduleEveryMinute($task, $schedule, $class)
    {
        $this->cron = $this->everyminute()->executionTime;

        $nextRun = $this->cron->getNextRunDate()->format('Y-m-d H:i:s');

        if ($task['force_next_run'] && $task['force_next_run'] == '1') {
            $task['force_next_run'] = null;
            if ($task['is_on_demand'] && $task['is_on_demand'] == '1') {
                $task['next_run'] = '-';
            } else {
                $task['next_run'] = 'Calculating Next Run...';
            }

            if (isset($task['org_schedule_id'])) {
                $task['schedule_id'] = $task['org_schedule_id'];
            }

            $this->tasks->update($task);
        } else if ($task['next_run'] !== $nextRun) {
            $task['next_run'] = $nextRun;

            $this->tasks->update($task);
        }

        $this->addToScheduler($task, $schedule, $class, $nextRun);
    }

    protected function scheduleEveryXMinutes($task, $schedule, $class)
    {
        $shouldSchedule = $this->shouldSchedule($task, $schedule);

        if ($shouldSchedule) {
            $this->addToScheduler($task, $schedule, $class, $shouldSchedule);
        }
    }

    protected function scheduleEveryXMinutesBetween($task, $schedule, $class)
    {
        $currentHour = (int) date("H");
        $currentMinute = (int) date("i");
        $startTime = explode(':', $schedule['params']['start']);
        $startHour = (int) $startTime[0];
        $startMinute = (int) $startTime[1];
        $endTime = explode(':', $schedule['params']['end']);
        $endHour = (int) $endTime[0];
        $endMinute = (int) $endTime[1];

        $betweenHour = false;
        $betweenMinutes = false;

        if ($currentHour >= $startHour && $currentHour < $endHour) {
            $betweenHour = true;
        }

        if ($betweenHour) {
            if ($endMinute > 0) {
                if ($currentMinute >= $startMinute || $currentMinute <= $endMinute) {
                    $betweenMinutes = true;
                }
            } else {
                if ($currentMinute >= $startMinute) {
                    $betweenMinutes = true;
                }
            }
        }

        if ($betweenHour && $betweenMinutes) {
            $shouldSchedule = $this->shouldSchedule($task, $schedule);
        } else {
            if ($currentHour >= $endHour) {
                $tomorrow = (Carbon::now()->addDay());

                $nextRun = $tomorrow->format('Y-m-d') . ' ' .  $schedule['params']['start'] . ':00';

                if ($task['next_run'] !== $nextRun) {
                    $task['next_run'] = $nextRun;

                    $this->tasks->update($task);
                }
            } else if ($currentHour < $startHour) {
                $today = Carbon::now();

                $nextRun = $today->format('Y-m-d') . ' ' .  $schedule['params']['start'] . ':00';

                if ($task['next_run'] !== $nextRun) {
                    $task['next_run'] = $nextRun;

                    $this->tasks->update($task);
                }
            }

            $shouldSchedule = false;
        }

        if ($shouldSchedule) {
            $this->addToScheduler($task, $schedule, $class, $shouldSchedule);
        }
    }

    protected function scheduleHourly($task, $schedule, $class)
    {
        $shouldSchedule = $this->shouldSchedule($task, $schedule);

        if ($shouldSchedule) {
            $this->addToScheduler($task, $schedule, $class, $shouldSchedule);
        }
    }

    protected function scheduleDaily($task, $schedule, $class)
    {
        $shouldSchedule = $this->shouldSchedule($task, $schedule);

        if ($shouldSchedule) {
            $this->addToScheduler($task, $schedule, $class, $shouldSchedule);
        }
    }

    protected function scheduleWeekly($task, $schedule, $class)
    {
        if (!in_array($this->dayOfWeek, $schedule['params']['weekly_days'])) {
            return false;
        }

        $shouldSchedule = $this->shouldSchedule($task, $schedule);

        if ($shouldSchedule) {
            $this->addToScheduler($task, $schedule, $class, $shouldSchedule);
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

        $shouldSchedule = $this->shouldSchedule($task, $schedule);

        if ($shouldSchedule) {
            $this->addToScheduler($task, $schedule, $class, $shouldSchedule);
        }
    }

    //Only Schedule if less than 1 minute so its schedules for next run.
    protected function shouldSchedule($task, $schedule)
    {
        if ($schedule['type'] === 'everyxminutes' ||
            $schedule['type'] === 'everyxminutesbetween'
        ) {
            $this->cron =
                $this->everyminute(
                    (int) $schedule['params']['minutes']
                )->executionTime;

            // $secsLeft = $this->cron->getNextRunDate()->getTimestamp() - time();
            $nextRun = $this->cron->getNextRunDate()->format('Y-m-d H:i:s');
        } else if ($schedule['type'] === 'hourly') {
            $this->cron =
                $this->hourly(
                    (int) $schedule['params']['hourly_minutes']
                )->executionTime;

            // $secsLeft = $this->cron->getNextRunDate()->getTimestamp() - time();
            $nextRun = $this->cron->getNextRunDate()->format('Y-m-d H:i:s');
        } else if ($schedule['type'] === 'daily') {
            $this->cron =
                $this->daily(
                    (int) $schedule['params']['daily_hours'],
                    (int) $schedule['params']['daily_minutes']
                )->executionTime;

            // $secsLeft = $this->cron->getNextRunDate()->getTimestamp() - time();
            $nextRun = $this->cron->getNextRunDate()->format('Y-m-d H:i:s');

            if ($task['next_run'] !== $this->cron->getNextRunDate()) {
                $this->tasks->update($task);
            }
        } else if ($schedule['type'] === 'weekly') {
            $this->cron =
                $this->weekly(
                    $this->dayOfWeek,
                    (int) $schedule['params']['weekly_hours'],
                    (int) $schedule['params']['weekly_minutes']
                )->executionTime;

            $secsLeft = $this->cron->getNextRunDate()->getTimestamp() - time();

            $nextRun = $this->cron->getNextRunDate()->format('Y-m-d H:i:s');

            if ($secsLeft > 43200) {//gt 12 hrs means the time has passed so get next time.
                if (count($schedule['params']['weekly_days']) > 1) {
                    if ($this->dayOfWeek == Arr::last($schedule['params']['weekly_days'])) {//If Saturday, the next day of execution will be 1st of array.
                        $this->cron =
                            $this->weekly(
                                (int) Arr::first($schedule['params']['weekly_days']),
                                (int) $schedule['params']['weekly_hours'],
                                (int) $schedule['params']['weekly_minutes']
                            )->executionTime;

                        $nextRun = $this->cron->getNextRunDate()->format('Y-m-d H:i:s');
                    } else {
                        $dayOfWeekKey = array_search($this->dayOfWeek, $schedule['params']['weekly_days']);

                        $nextKey = prefix_get_next_key_array($schedule['params']['weekly_days'], $dayOfWeekKey);

                        $this->cron =
                            $this->weekly(
                                (int) $schedule['params']['weekly_days'][$nextKey],
                                (int) $schedule['params']['weekly_hours'],
                                (int) $schedule['params']['weekly_minutes']
                            )->executionTime;

                        $nextRun = $this->cron->getNextRunDate()->format('Y-m-d H:i:s');
                    }
                }
            }
        } else if ($schedule['type'] === 'monthly') {
            $this->cron =
                $this->monthly(
                    (int) $this->month,
                    (int) $this->dateOfMonth,
                    (int) $schedule['params']['monthly_hours'],
                    (int) $schedule['params']['monthly_minutes']
                )->executionTime;

            $secsLeft = $this->cron->getNextRunDate()->getTimestamp() - time();

            $nextRun = $this->cron->getNextRunDate()->format('Y-m-d H:i:s');

            if ($secsLeft > 86400) { //gt 1 day means the time has passed so get next time.
                if (count($schedule['params']['monthly_months']) > 1) {
                    if ($this->month == Arr::last($schedule['params']['monthly_months'])) {//If December, the next day of execution will be 1st of array.
                        $this->cron =
                            $this->monthly(
                                (int) Arr::first($schedule['params']['monthly_months']),
                                (int) $this->dateOfMonth,
                                (int) $schedule['params']['monthly_hours'],
                                (int) $schedule['params']['monthly_minutes']
                            )->executionTime;

                        $nextRun = $this->cron->getNextRunDate()->format('Y-m-d H:i:s');
                    } else {
                        $monthKey = array_search($this->month, $schedule['params']['monthly_months']);

                        $nextKey = prefix_get_next_key_array($schedule['params']['monthly_months'], $monthKey);

                        $this->cron =
                            $this->monthly(
                                (int) $schedule['params']['monthly_months'][$nextKey],
                                (int) $this->dateOfMonth,
                                (int) $schedule['params']['monthly_hours'],
                                (int) $schedule['params']['monthly_minutes']
                            )->executionTime;

                        $nextRun = $this->cron->getNextRunDate()->format('Y-m-d H:i:s');
                    }
                }
            }
        }

        if (!$task['next_run'] || $task['next_run'] !== $nextRun) {
            $task['next_run'] = $nextRun;
            $this->tasks->update($task);
        }

        if ($this->cron->isDue()) {
            if ($task['status'] != 1) {
                $task['status'] = 1;
                $this->tasks->update($task);
            }

            return $nextRun;
        }

        return false;
    }

    protected function addToScheduler($task, $schedule, $class, $nextRun)
    {
        $this->checkIdleWorkers();

        $newJob = $this->addNewJob($task, $schedule);

        if ($newJob['worker_id'] == '0') {
            $newJob['run_on']               =  '-';
            $newJob['type']                 =  0;
            $newJob['status']               =  5;//Warning
            $newJob['execution_time']       =  '0.000';
            $newJob['response_code']        =  '1';
            $newJob['response_message']     =  'Task rescheduled for next run as no worker was available. Add more workers to avoid this situation.';
            $newJob['response_data']        =  '';

            $this->tasks->forceNextRun($task);

            $this->jobs->updateJob($newJob);

            return;
        } else {
            $updateWorker = $this->worker;
            $updateWorker['status'] = '1';

            $this->workers->updateWorker($updateWorker);
        }

        $args =
            [
                'job'   => $newJob,
                'task'  => $task
            ];

        if ($schedule['type'] === 'everyminute') {
            $this->scheduler->call(
                (new $class)->run($args),
                [],
                $task['id'] . '-' . $schedule['type']
            )->everyminute();
        } else if ($schedule['type'] === 'everyxminutes') {
            $this->scheduler->call(
                (new $class)->run($args),
                [],
                $task['id'] . '-' . $schedule['type']
            )->everyminute(
                (int) $schedule['params']['minutes']
            );
        } else if ($schedule['type'] === 'everyxminutesbetween') {
            $this->scheduler->call(
                (new $class)->run($args),
                [],
                $task['id'] . '-' . $schedule['type']
            )->everyminute(
                (int) $schedule['params']['minutes']
            );
        } else if ($schedule['type'] === 'hourly') {
            $this->scheduler->call(
                (new $class)->run($args),
                [],
                $task['id'] . '-' . $schedule['type']
            )->hourly(
                (int) $schedule['params']['hourly_minutes']
            );
        } else if ($schedule['type'] === 'daily') {
            $this->scheduler->call(
                (new $class)->run($args),
                [],
                $task['id'] . '-' . $schedule['type']
            )->daily(
                (int) $schedule['params']['daily_hours'],
                (int) $schedule['params']['daily_minutes']
            );
        } else if ($schedule['type'] === 'weekly') {
            $this->scheduler->call(
                (new $class)->run($args),
                [],
                $task['id'] . '-' . $schedule['type'] . '-' . $day
            )->weekly(
                $this->dayOfWeek,
                (int) $schedule['params']['weekly_hours'],
                (int) $schedule['params']['weekly_minutes']
            );
        } else if ($schedule['type'] === 'monthly') {
            $this->scheduler->call(
                (new $class)->run($args),
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

    protected function addNewJob($task, $schedule)
    {
        $addJob = $this->jobs->addJob(
            [
                'task_id'       => $task['id'],
                'worker_id'     => $this->worker['id'],
                'run_on'        => date('Y-m-d H:i:s'),
                'type'          => 0,
                'status'        => 1//Scheduled
            ]
        );

        if ($addJob) {
            $this->scheduledJobs[$task['id'] . '-' . $schedule['type']] = $this->jobs->packagesData->responseData;

            $task = $this->basepackages->workers->tasks->getById($task['id'], false, false);

            $task['via_job'] = 1;
            $task['status'] = 1;//Scheduled

            $this->tasks->updateTask($task);

            return $this->jobs->packagesData->responseData;
        }
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


    protected function checkIdleWorkers()
    {
        $this->workers = (new WorkersWorkers())->init(true);

        $idleWorkers = $this->workers->init()->getIdleWorkers();

        if ($idleWorkers && count($idleWorkers) > 0) {
            $this->idleWorkers = $idleWorkers;

            $this->worker = $this->idleWorkers[0];

            return true;
        }

        $this->idleWorkers = [];

        $this->worker['id'] = 0;

        return false;
    }

    protected function releaseWorkers()
    {
        if (count($this->scheduledJobs) > 0) {
            foreach ($this->scheduledJobs as $scheduledJob) {
                if ($scheduledJob['worker_id'] &&
                    $scheduledJob['worker_id'] != '' &&
                    $scheduledJob['worker_id'] != '0'
                ) {
                    $worker = $this->workers->getById($scheduledJob['worker_id'], false, false);

                    if ($worker) {
                        $worker['status'] = 0;

                        $this->workers->updateWorker($worker);
                    }
                }
            }
        }
    }
}