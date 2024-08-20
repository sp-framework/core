<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages;

use GO\Scheduler;
use Carbon\Carbon;
use GO\Traits\Interval;
use System\Base\BasePackage;
use League\Flysystem\UnableToReadFile;
use League\Flysystem\FilesystemException;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Workers\Jobs;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Workers\Calls;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Workers\Tasks;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Workers\Schedules;
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

    protected $enabledTasks;

    protected $availableCalls;

    protected $outputDir;

    protected $calls;

    public function init(bool $resetCache = false)
    {
        $this->workers = (new WorkersWorkers())->init(true);

        $this->schedules = (new Schedules())->init(true);

        $this->tasks = (new Tasks())->init(true);

        $this->jobs = (new Jobs())->init();

        if ($this->checkPath('var/workers')) {
            $this->schedulerSettings['tempDir'] = base_path('var/workers');
        }
        if ($this->checkPath('var/workers/output')) {
            $this->outputDir = base_path('var/workers/output');
        }

        $this->scheduler = new Scheduler($this->schedulerSettings);

        $this->dayOfWeek = date('w');

        $this->dateOfMonth = date('j');

        $this->month = date('n');

        $this->enabledTasks = $this->tasks->getEnabledTasks();

        $this->availableCalls = array_keys($this->tasks->getAllCalls());

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
        foreach ($this->enabledTasks as $taskKey => $task) {
            $schedule = $this->schedules->getSchedulesSchedule($task['schedule_id']);
            $class = null;

            if (in_array($task['call'], $this->availableCalls)) {
                $class = 'System\\Base\\Providers\\BasepackagesServiceProvider\\Packages\\Workers\\Calls\\' . ucfirst($task['call']);
            } else {
                $task['enabled'] = 0;
                $task['status'] = 3;//Error
                $task['result'] = 'Task call not found!';

                $this->tasks->update($task);

                continue;
            }

            if ($schedule['type'] === 'everyxseconds' || $schedule['type'] === 'everyminute') {
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
        }
        // var_dump($this->enabledTasks);
        // var_dump($this->scheduledJobs);
        // die();
        // var_dump($this->worker);
        // var_dump($this->scheduler->getQueuedJobs()[0]->getId());
        // var_dump($this->scheduler);
        // die();
        $this->scheduler->run();
        // var_dump('done');
        // $failedJobs = $this->scheduler->getFailedJobs();
        // var_dump($failedJobs);
        // if (count($failedJobs) > 0) {
        //     foreach ($failedJobs as $failedJobKey => $failedJob) {
        //         $id = $failedJob->getJob()->getId();
        //         $this->scheduledJobs[$id]['status'] = 4;//Error
        //         $this->scheduledJobs[$id]['response_code'] = ["1"];
        //         $this->scheduledJobs[$id]['response_message'] = [$failedJob->getException()->getMessage()];
        //         $this->scheduledJobs[$id]['response_data'] = $this->helper->encode([]);

        //         $this->jobs->updateJob($this->scheduledJobs[$id]);
        //     }
        // }

        // $executedJobs = $this->scheduler->getExecutedJobs();

        // if (count($executedJobs) > 0) {
        //     foreach ($executedJobs as $executedJobKey => $executedJob) {
        //         // var_dump($executedJob);die();
        //         // $id = $executedJob->getJob()->getId();
        //         // var_dump($executedJob->getOutput());
        //         // $this->scheduledJobs[$id]['status'] = 4;//Error
        //         // $this->scheduledJobs[$id]['result'] = $failedJob->getException()->getMessage();

        //         // $this->jobs->updateJob($this->scheduledJobs[$id]);
        //     }
        // }

        $stuckTasks = $this->tasks->getRunningTasks();
        // var_dump($stuckTasks);
        if ($stuckTasks && count($stuckTasks) > 0) {
            foreach ($this->scheduledJobs as  $scheduledJob) {
                foreach ($stuckTasks as $stuckTaskKey => $stuckTask) {
                    if ($scheduledJob['task_id'] === $stuckTask['id']) {
                        $stuckTask['status'] = 1;//Reschedule
                        $stuckTask['result'] = 'Task was stuck and is free now!';

                        $this->tasks->update($stuckTask);

                        $stuckJob = $this->jobs->getById($scheduledJob['id']);

                        if ($stuckJob && $stuckJob['status'] != 4) {
                            $stuckJob['status'] = 4;
                            $stuckJob['response_code'] = ["1"];
                            $stuckJob['response_message'] = ['Job was stuck and is free now!'];

                            $this->jobs->updateJob($stuckJob);
                        }
                    }
                }
            }
        }

        $this->releaseWorkers();
    }

    public function exec($taskId, $jobId)
    {
        //Check to make sure that job ID is valid and not randomly used as exec should be executed by workers and not via cli
        $task = $this->basepackages->workers->tasks->getById($taskId, false, false);
        $job = $this->basepackages->workers->jobs->getById($jobId, false, false);
        $schedule = $this->schedules->getSchedulesSchedule($task['schedule_id']);

        if (!$task || !$job) {
            echo "Task or job id is incorrect.";

            return false;
        }

        $class = 'System\\Base\\Providers\\BasepackagesServiceProvider\\Packages\\Workers\\Calls\\' . ucfirst($task['call']);
        $call = new $class;

        $args = ['task' => $task, 'job' => $job, 'schedule' => $schedule];

        if ($schedule && $schedule['type'] === 'everyxseconds') {
            $this->work($call, $args, $schedule);

            return;
        }

        $this->execRun($call, $args);
    }

    protected function execRun($call, $args)
    {
        try {
            $call->run($args)();
        } catch (\throwable $e) {
            $data['responseCode'] = ["1"];
            $data['responseMessage'] = [$e->getMessage()];
            $data['responseData'] = [];

            $call->addJobResult((object) $data, $args);
        }

        return;
    }

    protected function work($call, $args, $schedule)
    {
        try {
            $seconds = $schedule['params']['seconds'];

            if (is_string($seconds)) {
                $seconds = explode(',', $seconds);
            }

            foreach ($seconds as $key => $second) {
                if ((int) date('s') === (int) $second ||
                    (int) date('s') <= (int) $second ||
                    (int) $second === 0
                ) {
                    $this->execRun($call, $args);
                }

                if ($key !== array_key_last($seconds)) {
                    $sleepSeconds = (int) $seconds[$key + 1] - (int) date('s');

                    if ($sleepSeconds < 0) {
                        $sleepSeconds = 15;
                    }

                    sleep($sleepSeconds);
                }
            }
        } catch (\throwable $e) {
            $data['responseCode'] = ["1"];
            $data['responseMessage'] = [$e->getMessage()];
            $data['responseData'] = [];

            $call->addJobResult((object) $data, $args);

            return;
        }
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

        $this->addToScheduler($task, $schedule, $class);
    }

    protected function scheduleEveryXMinutes($task, $schedule, $class)
    {
        if ($this->shouldSchedule($task, $schedule)) {
            $this->addToScheduler($task, $schedule, $class);
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
            $this->addToScheduler($task, $schedule, $class);
        }
    }

    protected function scheduleHourly($task, $schedule, $class)
    {
        if ($this->shouldSchedule($task, $schedule)) {
            $this->addToScheduler($task, $schedule, $class);
        }
    }

    protected function scheduleDaily($task, $schedule, $class)
    {
        if ($this->shouldSchedule($task, $schedule)) {
            $this->addToScheduler($task, $schedule, $class);
        }
    }

    protected function scheduleWeekly($task, $schedule, $class)
    {
        if (!in_array($this->dayOfWeek, $schedule['params']['weekly_days'])) {
            return false;
        }

        if ($this->shouldSchedule($task, $schedule)) {
            $this->addToScheduler($task, $schedule, $class);
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
            $this->addToScheduler($task, $schedule, $class);
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
                    if ($this->dayOfWeek == $this->helper->last($schedule['params']['weekly_days'])) {//If Saturday, the next day of execution will be 1st of array.
                        $this->cron =
                            $this->weekly(
                                (int) $this->helper->first($schedule['params']['weekly_days']),
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
                    if ($this->month == $this->helper->last($schedule['params']['monthly_months'])) {//If December, the next day of execution will be 1st of array.
                        $this->cron =
                            $this->monthly(
                                (int) $this->helper->first($schedule['params']['monthly_months']),
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

    protected function addToScheduler($task, $schedule, $class)
    {
        $this->checkIdleWorkers();

        $newJob = $this->addNewJob($task, $schedule);

        if ($newJob['worker_id'] == '0') {
            $newJob['run_on']               =  ['-'];
            $newJob['type']                 =  0;
            $newJob['status']               =  5;//Warning
            $newJob['execution_time']       =  '0.000';
            $newJob['response_code']        =  ['1'];
            $newJob['response_message']     =  ['Task rescheduled for next run as no worker was available. Add more workers to avoid this situation.'];
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
                'job'       => $newJob,
                'task'      => $task,
                'schedule'  => $schedule
            ];

        if ($task['exec_type'] === 'call') {
            $this->scheduleCallSchedules($class, $args, $schedule);
        } else if ($task['exec_type'] === 'php') {
            $this->schedulePhpSchedules($class, $args, $schedule);
        } else if ($task['exec_type'] === 'raw') {
            $this->scheduleRawSchedules($class, $args, $schedule);
        }
    }

    protected function scheduleCallSchedules($class, $args, $schedule)
    {
        if ($schedule['type'] === 'everyminute') {
            $this->scheduler->call(
                (new $class)->run($args),
                [],
                $args['task']['id'] . '-' . $schedule['type']
            )->everyminute();
        } else if ($schedule['type'] === 'everyxminutes') {
            $this->scheduler->call(
                (new $class)->run($args),
                [],
                $args['task']['id'] . '-' . $schedule['type']
            )->everyminute(
                (int) $schedule['params']['minutes']
            );
        } else if ($schedule['type'] === 'everyxminutesbetween') {
            $this->scheduler->call(
                (new $class)->run($args),
                [],
                $args['task']['id'] . '-' . $schedule['type']
            )->everyminute(
                (int) $schedule['params']['minutes']
            );
        } else if ($schedule['type'] === 'hourly') {
            $this->scheduler->call(
                (new $class)->run($args),
                [],
                $args['task']['id'] . '-' . $schedule['type']
            )->hourly(
                (int) $schedule['params']['hourly_minutes']
            );
        } else if ($schedule['type'] === 'daily') {
            $this->scheduler->call(
                (new $class)->run($args),
                [],
                $args['task']['id'] . '-' . $schedule['type']
            )->daily(
                (int) $schedule['params']['daily_hours'],
                (int) $schedule['params']['daily_minutes']
            );
        } else if ($schedule['type'] === 'weekly') {
            $this->scheduler->call(
                (new $class)->run($args),
                [],
                $args['task']['id'] . '-' . $schedule['type'] . '-' . $day
            )->weekly(
                $this->dayOfWeek,
                (int) $schedule['params']['weekly_hours'],
                (int) $schedule['params']['weekly_minutes']
            );
        } else if ($schedule['type'] === 'monthly') {
            $this->scheduler->call(
                (new $class)->run($args),
                [],
                $args['task']['id'] . '-' . $schedule['type'] . '-' . $month
            )->monthly(
                (int) $this->month,
                (int) $this->dateOfMonth,
                (int) $schedule['params']['monthly_hours'],
                (int) $schedule['params']['monthly_minutes']
            );
        }
    }

    protected function schedulePhpSchedules($class, $args, $schedule)
    {
        $phpArgs = [
            'workers' => null,
            'exec' => null,
            $args['task']['id'] => null,
            $args['job']['id'] => null
        ];

        if (property_exists($class, 'php_args') && method_exists($class,'getPhpArgs')) {
            $phpArgs = array_merge($phpArgs, (new $class)->getPhpArgs());
        }

        $phpScript = base_path('public/index.php');

        if ($schedule['type'] === 'everyxseconds' ||
            $schedule['type'] === 'everyminute'
        ) {
            $this->scheduler->php(
                $phpScript,
                null,
                $phpArgs,
                $args['task']['id'] . '-' . $schedule['type']
            )->
            onlyOne(null, $this->removeStuckLockFile($args['task'], $schedule, $phpScript))->
            output($this->outputDir . '/' . $args['task']['id'] . '-' . $schedule['type'] . '.log')->
            before(function() use ($args) {
                $this->processBefore($args);
            })->
            then(function () use ($args) {
                $this->processThen($args);
            }, true)->
            everyminute();
        } else if ($schedule['type'] === 'everyxminutes') {
            $this->scheduler->php(
                $phpScript,
                null,
                $phpArgs,
                $args['task']['id'] . '-' . $schedule['type']
            )->
            onlyOne(null, $this->removeStuckLockFile($args['task'], $schedule, $phpScript))->
            output($this->outputDir . '/' . $args['task']['id'] . '-' . $schedule['type'] . '.log')->
            before(function() use ($args) {
                $this->processBefore($args);
            })->
            then(function () use ($args) {
                $this->processThen($args);
            }, true)->
            everyminute(
                (int) $schedule['params']['minutes']
            );
        } else if ($schedule['type'] === 'everyxminutesbetween') {
            $this->scheduler->php(
                $phpScript,
                null,
                $phpArgs,
                $args['task']['id'] . '-' . $schedule['type']
            )->
            onlyOne(null, $this->removeStuckLockFile($args['task'], $schedule, $phpScript))->
            output($this->outputDir . '/' . $args['task']['id'] . '-' . $schedule['type'] . '.log')->
            before(function() use ($args) {
                $this->processBefore($args);
            })->
            then(function () use ($args) {
                $this->processThen($args);
            }, true)->
            everyminute(
                (int) $schedule['params']['minutes']
            );
        } else if ($schedule['type'] === 'hourly') {
            $this->scheduler->php(
                $phpScript,
                null,
                $phpArgs,
                $args['task']['id'] . '-' . $schedule['type']
            )->
            onlyOne(null, $this->removeStuckLockFile($args['task'], $schedule, $phpScript))->
            output($this->outputDir . '/' . $args['task']['id'] . '-' . $schedule['type'] . '.log')->
            before(function() use ($args) {
                $this->processBefore($args);
            })->
            then(function () use ($args) {
                $this->processThen($args);
            }, true)->
            hourly(
                (int) $schedule['params']['hourly_minutes']
            );
        } else if ($schedule['type'] === 'daily') {
            $this->scheduler->php(
                $phpScript,
                null,
                $phpArgs,
                $args['task']['id'] . '-' . $schedule['type']
            )->
            onlyOne(null, $this->removeStuckLockFile($args['task'], $schedule, $phpScript))->
            output($this->outputDir . '/' . $args['task']['id'] . '-' . $schedule['type'] . '.log')->
            before(function() use ($args) {
                $this->processBefore($args);
            })->
            then(function () use ($args) {
                $this->processThen($args);
            }, true)->
            daily(
                (int) $schedule['params']['daily_hours'],
                (int) $schedule['params']['daily_minutes']
            );
        } else if ($schedule['type'] === 'weekly') {
            $this->scheduler->php(
                $phpScript,
                null,
                $phpArgs,
                $args['task']['id'] . '-' . $schedule['type'] . '-' . $day
            )->
            onlyOne(null, $this->removeStuckLockFile($args['task'], $schedule, $phpScript))->
            output($this->outputDir . '/' . $args['task']['id'] . '-' . $schedule['type'] . '.log')->
            before(function() use ($args) {
                $this->processBefore($args);
            })->
            then(function () use ($args) {
                $this->processThen($args);
            }, true)->
            weekly(
                $this->dayOfWeek,
                (int) $schedule['params']['weekly_hours'],
                (int) $schedule['params']['weekly_minutes']
            );
        } else if ($schedule['type'] === 'monthly') {
            $this->scheduler->php(
                $phpScript,
                null,
                $phpArgs,
                $args['task']['id'] . '-' . $schedule['type'] . '-' . $month
            )->
            onlyOne(null, $this->removeStuckLockFile($args['task'], $schedule, $phpScript))->
            output($this->outputDir . '/' . $args['task']['id'] . '-' . $schedule['type'] . '.log')->
            before(function() use ($args) {
                $this->processBefore($args);
            })->
            then(function () use ($args) {
                $this->processThen($args);
            }, true)->
            monthly(
                (int) $this->month,
                (int) $this->dateOfMonth,
                (int) $schedule['params']['monthly_hours'],
                (int) $schedule['params']['monthly_minutes']
            );
        }
    }

    protected function scheduleRawSchedules($class, $args, $schedule)
    {
        $rawArgs = [];

        if (property_exists($class, 'raw_args') && method_exists($class,'getRawArgs')) {
            $rawArgs = (new $class)->getRawArgs();
        }

        if ($schedule['type'] === 'everyminute') {
            $this->scheduler->raw(
                $task['raw'],
                $rawArgs,
                $args['task']['id'] . '-' . $schedule['type']
            )->everyminute();
        } else if ($schedule['type'] === 'everyxminutes') {
            $this->scheduler->raw(
                $task['raw'],
                $rawArgs,
                $args['task']['id'] . '-' . $schedule['type']
            )->everyminute(
                (int) $schedule['params']['minutes']
            );
        } else if ($schedule['type'] === 'everyxminutesbetween') {
            $this->scheduler->raw(
                $task['raw'],
                $rawArgs,
                $args['task']['id'] . '-' . $schedule['type']
            )->everyminute(
                (int) $schedule['params']['minutes']
            );
        } else if ($schedule['type'] === 'hourly') {
            $this->scheduler->raw(
                $task['raw'],
                $rawArgs,
                $args['task']['id'] . '-' . $schedule['type']
            )->hourly(
                (int) $schedule['params']['hourly_minutes']
            );
        } else if ($schedule['type'] === 'daily') {
            $this->scheduler->raw(
                $task['raw'],
                $rawArgs,
                $args['task']['id'] . '-' . $schedule['type']
            )->daily(
                (int) $schedule['params']['daily_hours'],
                (int) $schedule['params']['daily_minutes']
            );
        } else if ($schedule['type'] === 'weekly') {
            $this->scheduler->raw(
                $task['raw'],
                $rawArgs,
                $args['task']['id'] . '-' . $schedule['type'] . '-' . $day
            )->weekly(
                $this->dayOfWeek,
                (int) $schedule['params']['weekly_hours'],
                (int) $schedule['params']['weekly_minutes']
            );
        } else if ($schedule['type'] === 'monthly') {
            $this->scheduler->raw(
                $task['raw'],
                $rawArgs,
                $args['task']['id'] . '-' . $schedule['type'] . '-' . $month
            )->monthly(
                (int) $this->month,
                (int) $this->dateOfMonth,
                (int) $schedule['params']['monthly_hours'],
                (int) $schedule['params']['monthly_minutes']
            );
        }
    }

    protected function processBefore($args)
    {
        $this->calls = new Calls;

        $this->calls->updateJobTask(2, $args);
    }

    protected function processThen($args)
    {
        $args['task']['pid'] = $this->getTaskProcessId($args['task'], base_path('public/index.php workers exec'));

        $this->basepackages->workers->tasks->updateTask($args['task']);
        // var_dump($args);
        // $this->calls->updateJobTask(2, $args);

        // try {
        //     $output = $this->localContent->read('var/workers/output/' . $args['task']['id'] . '-' . $schedule['type'] . '.log');
        // } catch (\throwable | FilesystemException | UnableToReadFile $exception) {
        //     var_dump($exception);
        //     $output = '';
        // }

        // $data['responseCode'] = 0;
        // $data['responseMessage'] = 'OK';
        // $data['responseData'] = ['output' => $output];
        // $this->calls->addJobResult((object) $data, $args);

        // // var_dump($args);die();
        // $this->calls->updateJobTask(3, $args);
    }

    protected function removeStuckLockFile($task, $schedule, $phpScript = null, $rawCommand = null)
    {
        if (($phpScript && $this->getTaskProcessId($task, $phpScript) === null) ||
            ($rawCommand && $this->getTaskProcessId($task, $rawCommand) === null)
        ) {
            if (file_exists(base_path('var/workers/' . $task['id'] . '-' . $schedule['type'] . '.lock'))) {
                unlink(base_path('var/workers/' . $task['id'] . '-' . $schedule['type'] . '.lock'));
            }
        }
    }

    protected function getTaskProcessId($task, $phpScript = null, $rawCommand = null)
    {
        $pid = null;

        if ($task['exec_type'] === 'php' && $phpScript) {
            $grep = PHP_BINARY === '' ? '/usr/bin/php' : PHP_BINARY . ' ' . $phpScript;

            exec('ps -ef | grep "' . $grep . '"', $output);

            if (is_array($output) && count($output) > 0) {
                foreach ($output as $outputValue) {
                    if (str_contains($outputValue, $grep) && !str_contains($outputValue, 'grep')) {
                        $outputValue = explode(' ', $outputValue);

                        foreach ($outputValue as $value) {
                            if ((int) $value !== 0) {
                                $pid = $value;
                                break;
                            }
                        }

                    }
                }
            }
        } else if ($task['exec_type'] === 'raw' && $rawCommand) {
            //
        }
        return $pid;
    }

    protected function addNewJob($task, $schedule)
    {
        $addJob = $this->jobs->addJob(
            [
                'task_id'       => $task['id'],
                'worker_id'     => $this->worker['id'],
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

    protected function checkPath($path)
    {
        if (!is_dir(base_path($path))) {
            if (!mkdir(base_path($path), 0777, true)) {
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