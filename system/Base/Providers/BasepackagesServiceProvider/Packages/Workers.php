<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages;

use Carbon\Carbon;
use GO\Scheduler;
use GO\Traits\Interval;
use League\Flysystem\FilesystemException;
use League\Flysystem\UnableToReadFile;
use System\Base\BasePackage;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Workers\Calls;
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

    public $calls;

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

    public function init(bool $resetCache = false)
    {
        $this->workers = (new WorkersWorkers())->init(true);

        $this->tasks = (new Tasks())->init(true);

        $this->jobs = (new Jobs())->init();

        if (PHP_SAPI === 'cli' && !$this->checkIdleWorkers()) {
            if (!$this->releaseWorkers(true)) {
                $this->logger->log->alert('No Workers available at ' . date('Y-m-d H:i:s'));

                return false;//We quite as there are no available workers.
            }
        }

        $this->schedules = (new Schedules())->init(true);

        $this->calls = (new Calls())->init();

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

        $this->availableCalls = [];
        if ($this->calls->calls && count($this->calls->calls) > 0) {
            foreach ($this->calls->calls as $thisCalls) {
                if (!$thisCalls['package']) {
                    continue;
                }

                if (!isset($calls[$thisCalls['id']])) {
                    $calls[$thisCalls['id']] = [];
                }

                $this->availableCalls[$thisCalls['id']]['id'] = $thisCalls['id'];
                $this->availableCalls[$thisCalls['id']]['name'] = $thisCalls['name'];
                $this->availableCalls[$thisCalls['id']]['package_class'] = $thisCalls['package']['class'];
            }
        }

        return $this;
    }

    public function run()
    {
        foreach ($this->enabledTasks as $task) {
            $schedule = $this->schedules->getSchedulesSchedule($task['schedule_id']);

            if (isset($this->availableCalls[$task['cid']])) {
                $class = $this->getClass($task);
            }

            if (!isset($class)) {
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
            } else {
                if ($this->shouldSchedule($task, $schedule)) {
                    $this->addToScheduler($task, $schedule, $class);
                }
            }
        }

        $this->scheduler->run();

        $failedJobs = $this->scheduler->getFailedJobs();

        if (count($failedJobs) > 0) {
            foreach ($failedJobs as $failedJobKey => $failedJob) {
                $id = $failedJob->getJob()->getId();
                $this->scheduledJobs[$id]['status'] = 4;//Error
                $this->scheduledJobs[$id]['response_code'] = ["1"];
                $this->scheduledJobs[$id]['response_message'] = [$failedJob->getException()->getMessage()];
                $this->scheduledJobs[$id]['response_data'] = [];

                $this->jobs->updateJob($this->scheduledJobs[$id]);

                $task = $this->enabledTasks[$this->scheduledJobs[$id]['task_id']];

                $task['status'] = 3;//Error
                $task['force_next_run'] = null;
                $task['next_run'] = '-';
                $task['result'] = 'Job has error!';

                $this->tasks->update($task);
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

        if (isset($this->availableCalls[$task['cid']])) {
            $class = $this->getClass($task);
        }

        if (!isset($class)) {
            $task['enabled'] = 0;
            $task['status'] = 3;//Error
            $task['result'] = 'Task call not found!';

            $this->tasks->update($task);

            return false;
        }

        $call = new $class;

        $args = ['task' => $task, 'job' => $job, 'schedule' => $schedule];

        if ($schedule && $schedule['type'] === 'everyxseconds') {
            $this->work($call, $args, $schedule);

            return;
        }

        $callPackagesData = $this->execRun($call, $args);

        if ($callPackagesData && $callPackagesData->responseCode !== 0) {
            $data['responseCode'] = $callPackagesData->responseCode;
            $data['responseMessage'] = 'Error: ' . $callPackagesData->responseMessage;
            $data['responseData'] = $callPackagesData->responseData ?? [];

            $call->addJobResult((object) $data, $args);
        }
    }

    protected function execRun($call, $args)
    {
        try {
            $call->run($args);

            return $call->packagesData;
        } catch (\throwable $e) {
            $data['responseCode'] = 1;
            $data['responseMessage'] = 'Exception: ' . $e->getMessage();
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
                    $callPackagesData = $this->execRun($call, $args);

                    if ($callPackagesData && $callPackagesData->responseCode !== 0) {
                        $data['responseCode'] = $callPackagesData->responseCode;
                        $data['responseMessage'] = 'Error: ' . $callPackagesData->responseMessage;
                        $data['responseData'] = $callPackagesData->responseData ?? [];

                        $call->addJobResult((object) $data, $args);

                        return;
                    }
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
            $data['responseCode'] = 1;
            $data['responseMessage'] = 'Exception: ' . $e->getMessage();
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

    protected function shouldSchedule($task, $schedule)
    {
        //Only Schedule if less than 1 minute so its schedules for next run.
        if ($schedule['type'] === 'everyxminutes' ||
            $schedule['type'] === 'everyxminutesbetween'
        ) {
            $this->cron =
                $this->everyminute(
                    (int) $schedule['params']['minutes']
                )->executionTime;

            $nextRun = $this->cron->getNextRunDate()->format('Y-m-d H:i:s');
        } else if ($schedule['type'] === 'hourly') {
            $this->cron =
                $this->hourly(
                    (int) $schedule['params']['hourly_minutes']
                )->executionTime;

            $nextRun = $this->cron->getNextRunDate()->format('Y-m-d H:i:s');
        } else if ($schedule['type'] === 'daily') {
            $this->cron =
                $this->daily(
                    (int) $schedule['params']['daily_hours'],
                    (int) $schedule['params']['daily_minutes']
                )->executionTime;

            $nextRun = $this->cron->getNextRunDate()->format('Y-m-d H:i:s');

            if ($task['next_run'] !== $this->cron->getNextRunDate()) {
                $this->tasks->update($task);
            }
        } else if ($schedule['type'] === 'weekly') {
            $this->cron =
                $this->weekly(
                    (int) $this->helper->first($schedule['params']['weekly_days']),
                    (int) $schedule['params']['weekly_hours'],
                    (int) $schedule['params']['weekly_minutes']
                )->executionTime;

            $nextRun = $this->cron->getNextRunDate()->format('Y-m-d H:i:s');

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
        } else if ($schedule['type'] === 'monthly') {
            // Validate Date if someone is trying to set end of month.
            if ((int) $schedule['params']['monthly_day'] > 27) {
                $year = date('Y');
                if ((int) $this->helper->first($schedule['params']['monthly_months']) < $this->month) {
                    $year = $year + 1;//next year
                }

                if (!checkdate((int) $this->helper->first($schedule['params']['monthly_months']), (int) $schedule['params']['monthly_day'], date('Y'))) {
                    $correctDate = new \DateTime(date('y') . '-' . (int) $this->helper->first($schedule['params']['monthly_months']) . '-1');//First of month
                    $correctDate->modify('last day of this month');
                    $schedule['params']['monthly_day'] = $correctDate->format('d');
                }
            }

            $this->cron =
                $this->monthly(
                    (int) $this->helper->first($schedule['params']['monthly_months']),
                    (int) $schedule['params']['monthly_day'],
                    (int) $schedule['params']['monthly_hours'],
                    (int) $schedule['params']['monthly_minutes']
                )->executionTime;

            $nextRun = $this->cron->getNextRunDate()->format('Y-m-d H:i:s');

            if (count($schedule['params']['monthly_months']) > 1) {
                if ($this->month == $this->helper->last($schedule['params']['monthly_months'])) {//If December, the next day of execution will be 1st of array.
                    $year = date('Y');
                    if ((int) $this->helper->first($schedule['params']['monthly_months']) < $this->month) {
                        $year = $year + 1;//next year
                    }

                    if (!checkdate((int) $this->helper->first($schedule['params']['monthly_months']), (int) $schedule['params']['monthly_day'], date('Y'))) {
                        $correctDate = new \DateTime(date('y') . '-' . (int) $this->helper->first($schedule['params']['monthly_months']) . '-1');//First of month
                        $correctDate->modify('last day of this month');
                        $schedule['params']['monthly_day'] = $correctDate->format('d');
                    }

                    $this->cron =
                        $this->monthly(
                            (int) $this->helper->first($schedule['params']['monthly_months']),
                            (int) $schedule['params']['monthly_day'],
                            (int) $schedule['params']['monthly_hours'],
                            (int) $schedule['params']['monthly_minutes']
                        )->executionTime;

                    $nextRun = $this->cron->getNextRunDate()->format('Y-m-d H:i:s');
                } else {
                    $monthKey = array_search($this->month, $schedule['params']['monthly_months']);

                    if ($monthKey) {
                        $nextKey = prefix_get_next_key_array($schedule['params']['monthly_months'], $monthKey);

                        $year = date('Y');
                        if ((int) $schedule['params']['monthly_months'][$nextKey] < $this->month) {
                            $year = $year + 1;//next year
                        }

                        if (!checkdate((int) $schedule['params']['monthly_months'][$nextKey], (int) $schedule['params']['monthly_day'], date('Y'))) {
                            $correctDate = new \DateTime(date('y') . '-' . (int) $this->helper->first($schedule['params']['monthly_months']) . '-1');//First of month
                            $correctDate->modify('last day of this month');
                            $schedule['params']['monthly_day'] = $correctDate->format('d');
                        }

                        $this->cron =
                            $this->monthly(
                                (int) $schedule['params']['monthly_months'][$nextKey],
                                (int) $schedule['params']['monthly_day'],
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
        $this->checkIdleWorkers(true);

        if ($newJob = $this->addNewJob($task, $schedule)) {
            $args =
                [
                    'job'       => $newJob,
                    'task'      => $task,
                    'schedule'  => $schedule
                ];

            $this->addTaskToScheduler($task, $class, $args, $schedule);
        }
    }

    protected function addTaskToScheduler($task, $class, $args, $schedule)
    {
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
                function() use ($class, $args) {
                    (new $class)->run($args);
                },
                [],
                $args['task']['id'] . '-' . $schedule['type']
            )->everyminute();
        } else if ($schedule['type'] === 'everyxminutes') {
            $this->scheduler->call(
                function() use ($class, $args) {
                    (new $class)->run($args);
                },
                [],
                $args['task']['id'] . '-' . $schedule['type']
            )->everyminute(
                (int) $schedule['params']['minutes']
            );
        } else if ($schedule['type'] === 'everyxminutesbetween') {
            $this->scheduler->call(
                function() use ($class, $args) {
                    (new $class)->run($args);
                },
                [],
                $args['task']['id'] . '-' . $schedule['type']
            )->everyminute(
                (int) $schedule['params']['minutes']
            );
        } else if ($schedule['type'] === 'hourly') {
            $this->scheduler->call(
                function() use ($class, $args) {
                    (new $class)->run($args);
                },
                [],
                $args['task']['id'] . '-' . $schedule['type']
            )->hourly(
                (int) $schedule['params']['hourly_minutes']
            );
        } else if ($schedule['type'] === 'daily') {
            $this->scheduler->call(
                function() use ($class, $args) {
                    (new $class)->run($args);
                },
                [],
                $args['task']['id'] . '-' . $schedule['type']
            )->daily(
                (int) $schedule['params']['daily_hours'],
                (int) $schedule['params']['daily_minutes']
            );
        } else if ($schedule['type'] === 'weekly') {
            $this->scheduler->call(
                function() use ($class, $args) {
                    (new $class)->run($args);
                },
                [],
                $args['task']['id'] . '-' . $schedule['type'] . '-' . $day
            )->weekly(
                $this->dayOfWeek,
                (int) $schedule['params']['weekly_hours'],
                (int) $schedule['params']['weekly_minutes']
            );
        } else if ($schedule['type'] === 'monthly') {
            $this->scheduler->call(
                function() use ($class, $args) {
                    (new $class)->run($args);
                },
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
            'taskId' => $args['task']['id'],
            'jobId' => $args['job']['id']
        ];

        if (method_exists($class,'getPhpArgs')) {
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
        $class = new $class;

        $rawCmd = null;

        if (method_exists($class, 'getRawCmd')) {
            $rawCmd = $class->getRawCmd();
        }

        if (is_null($rawCmd)) {
            $this->calls->packagesData->responseCode = 1;
            $this->calls->packagesData->responseMessage = 'Raw command not provided in the Calls class file.';

            $this->calls->addJobResult($this->calls->packagesData, $args);

            $this->calls->updateJobTask(4, $args);

            return false;
        }

        $rawArgs = [];

        if (method_exists($class,'getRawArgs')) {
            $rawArgs = $class->getRawArgs();
        }

        if ($schedule['type'] === 'everyminute') {
            $this->scheduler->raw(
                $rawCmd,
                $rawArgs,
                $args['task']['id'] . '-' . $schedule['type']
            )->
            onlyOne(null, $this->removeStuckLockFile($args['task'], $schedule, null, $rawCmd))->
            output($this->outputDir . '/' . $args['task']['id'] . '-' . $schedule['type'] . '.log')->
            before(function() use ($args) {
                $this->processBefore($args);
            })->
            then(function () use ($args, $rawCmd) {
                $this->processThen($args, $rawCmd);
            }, true)
            ->everyminute();
        } else if ($schedule['type'] === 'everyxminutes') {
            $this->scheduler->raw(
                $rawCmd,
                $rawArgs,
                $args['task']['id'] . '-' . $schedule['type']
            )->everyminute(
                (int) $schedule['params']['minutes']
            );
        } else if ($schedule['type'] === 'everyxminutesbetween') {
            $this->scheduler->raw(
                $rawCmd,
                $rawArgs,
                $args['task']['id'] . '-' . $schedule['type']
            )->
            onlyOne(null, $this->removeStuckLockFile($args['task'], $schedule, null, $rawCmd))->
            output($this->outputDir . '/' . $args['task']['id'] . '-' . $schedule['type'] . '.log')->
            before(function() use ($args) {
                $this->processBefore($args);
            })->
            then(function () use ($args, $rawCmd) {
                $this->processThen($args, $rawCmd);
            }, true)
            ->everyminute(
                (int) $schedule['params']['minutes']
            );
        } else if ($schedule['type'] === 'hourly') {
            $this->scheduler->raw(
                $rawCmd,
                $rawArgs,
                $args['task']['id'] . '-' . $schedule['type']
            )->
            onlyOne(null, $this->removeStuckLockFile($args['task'], $schedule, null, $rawCmd))->
            output($this->outputDir . '/' . $args['task']['id'] . '-' . $schedule['type'] . '.log')->
            before(function() use ($args) {
                $this->processBefore($args);
            })->
            then(function () use ($args, $rawCmd) {
                $this->processThen($args, $rawCmd);
            }, true)
            ->hourly(
                (int) $schedule['params']['hourly_minutes']
            );
        } else if ($schedule['type'] === 'daily') {
            $this->scheduler->raw(
                $rawCmd,
                $rawArgs,
                $args['task']['id'] . '-' . $schedule['type']
            )->
            onlyOne(null, $this->removeStuckLockFile($args['task'], $schedule, null, $rawCmd))->
            output($this->outputDir . '/' . $args['task']['id'] . '-' . $schedule['type'] . '.log')->
            before(function() use ($args) {
                $this->processBefore($args);
            })->
            then(function () use ($args, $rawCmd) {
                $this->processThen($args, $rawCmd);
            }, true)
            ->daily(
                (int) $schedule['params']['daily_hours'],
                (int) $schedule['params']['daily_minutes']
            );
        } else if ($schedule['type'] === 'weekly') {
            $this->scheduler->raw(
                $rawCmd,
                $rawArgs,
                $args['task']['id'] . '-' . $schedule['type'] . '-' . $day
            )->
            onlyOne(null, $this->removeStuckLockFile($args['task'], $schedule, null, $rawCmd))->
            output($this->outputDir . '/' . $args['task']['id'] . '-' . $schedule['type'] . '.log')->
            before(function() use ($args) {
                $this->processBefore($args);
            })->
            then(function () use ($args, $rawCmd) {
                $this->processThen($args, $rawCmd);
            }, true)
            ->weekly(
                $this->dayOfWeek,
                (int) $schedule['params']['weekly_hours'],
                (int) $schedule['params']['weekly_minutes']
            );
        } else if ($schedule['type'] === 'monthly') {
            $this->scheduler->raw(
                $rawCmd,
                $rawArgs,
                $args['task']['id'] . '-' . $schedule['type'] . '-' . $month
            )->
            onlyOne(null, $this->removeStuckLockFile($args['task'], $schedule, null, $rawCmd))->
            output($this->outputDir . '/' . $args['task']['id'] . '-' . $schedule['type'] . '.log')->
            before(function() use ($args) {
                $this->processBefore($args);
            })->
            then(function () use ($args, $rawCmd) {
                $this->processThen($args, $rawCmd);
            }, true)
            ->monthly(
                (int) $this->month,
                (int) $this->dateOfMonth,
                (int) $schedule['params']['monthly_hours'],
                (int) $schedule['params']['monthly_minutes']
            );
        }
    }

    protected function processBefore($args)
    {
        $this->calls->updateJobTask(2, $args);
    }

    protected function processThen($args, $rawCmd = false)
    {
        if ($rawCmd) {
            $args['task']['pid'] = $this->getTaskProcessId($args['task'], null, $rawCmd);

            $this->packagesData->responseCode = 0;

            $this->packagesData->responseMessage = 'Ok';

            $this->packagesData->responseData = [];

            try {
                $this->packagesData->responseData = $this->localContent->read('var/workers/output/' . $args['task']['id'] . '-' . $args['schedule']['type'] . '.log');
            } catch (\throwable | UnableToReadFile $e) {
                $this->packagesData->responseMessage = $e->getMessage();
            }

            $this->calls->addJobResult($this->packagesData, $args);

            $this->calls->updateJobTask(3, $args);
        } else {
            $args['task']['pid'] = $this->getTaskProcessId($args['task'], base_path('public/index.php workers exec'));

            $this->basepackages->workers->tasks->updateTask($args['task']);
        }
    }

    protected function removeStuckLockFile($task, $schedule, $phpScript = null, $rawCommand = null)
    {
        if (($phpScript && $this->getTaskProcessId($task, $phpScript) === null) ||
            ($rawCommand && $this->getTaskProcessId($task, null, $rawCommand) === null)
        ) {
            if (file_exists(base_path('var/workers/' . $task['id'] . '-' . $schedule['type'] . '.lock'))) {
                unlink(base_path('var/workers/' . $task['id'] . '-' . $schedule['type'] . '.lock'));
            }
        }
    }

    protected function getTaskProcessId($task, $phpScript = null, $rawCommand = null)
    {
        $pid = null;

        $output = [];

        if ($task['exec_type'] === 'php' && $phpScript) {
            $grep = PHP_BINARY === '' ? '/usr/bin/php' : PHP_BINARY . ' ' . $phpScript;
        } else if ($task['exec_type'] === 'raw' && $rawCommand) {
            $grep = $rawCommand;
        }

        exec('ps -ef | grep -F \'' . $grep . '\'', $output);

        if (is_array($output) && count($output) > 0) {
            foreach ($output as $outputValue) {
                if (str_contains($outputValue, $grep) && str_contains($outputValue, 'ps -ef')) {
                    $outputValue = explode(' ', $outputValue);

                    foreach ($outputValue as $value) {
                        if ((int) $value !== 0) {
                            $pid = $value;

                            break 1;
                        }
                    }
                }
            }
        }

        return $pid;
    }

    protected function addNewJob($task, $schedule)
    {
        if ($this->worker['id'] === 0) {
            $task['status'] = 4;//Reschedule due to no workers

            $this->tasks->forceNextRun($task);

            return false;
        }

        if ($task['job_log_mode'] != '1') {
            $job = $this->jobs->getJobByMode($task);

            if (!$job) {
                $time = \Carbon\Carbon::now();

                if ($task['job_log_mode'] == '2') {
                    $time = $time->startOfHour()->timestamp;
                } else if ($task['job_log_mode'] == '3') {
                    $time = $time->startOfDay()->timestamp;
                } else if ($task['job_log_mode'] == '4') {
                    $time = $time->startOfMonth()->timestamp;
                } else if ($task['job_log_mode'] == '5') {
                    $time = $time->startOfYear()->timestamp;
                }

                $addJob = $this->jobs->addJob(
                    [
                        'task_id'       => $task['id'],
                        'worker_id'     => $this->worker['id'],
                        'type'          => 0,
                        'job_log_mode'  => $task['job_log_mode'],
                        'job_log_time'  => $time,
                        'cid'           => $task['cid'],
                        'status'        => 1//Scheduled
                    ]
                );

                if ($addJob) {
                    $job = $this->jobs->packagesData->responseData;
                }
            }
        } else {
            $addJob = $this->jobs->addJob(
                [
                    'task_id'       => $task['id'],
                    'worker_id'     => $this->worker['id'],
                    'type'          => 0,
                    'job_log_mode'  => 1,
                    'job_log_time'  => null,
                    'cid'           => $task['cid'],
                    'status'        => 1//Scheduled
                ]
            );

            if ($addJob) {
                $job = $this->jobs->packagesData->responseData;
            }
        }

        if (isset($job) && is_array($job)) {
            $this->scheduledJobs[$task['id'] . '-' . $schedule['type']] = $job;

            $task = $this->basepackages->workers->tasks->getById($task['id'], false, false);

            $task['status'] = 1;//Scheduled

            $this->tasks->updateTask($task);

            $updateWorker = $this->worker;

            $updateWorker['status'] = '1';

            $this->workers->updateWorker($updateWorker);

            return $job;
        }

        $this->logger->log->alert('Unable to add job for task ' . $task['id'] . ' at ' . date('Y-m-d H:i:s'));

        return false;
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

    protected function checkIdleWorkers($getNextIdleWorker = false)
    {
        $idleWorkers = $this->workers->getIdleWorkers();

        if ($idleWorkers && count($idleWorkers) > 0) {
            $this->idleWorkers = $idleWorkers;

            if ($getNextIdleWorker) {
                $this->worker = $this->idleWorkers[0];
            }

            return true;
        }

        $this->idleWorkers = [];

        $this->worker['id'] = 0;

        return false;
    }

    protected function releaseWorkers($force = false)
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

        if ($force) {
            return $this->workers->forceReleaseWorkers($this->jobs->getRunningJobs());
        }
    }

    protected function getClass($task)
    {
        $class = null;

        if (str_starts_with($this->availableCalls[$task['cid']]['package_class'], 'System')) {
            $class = 'System\\Base\\Providers\\BasepackagesServiceProvider\\Packages\\Workers\\Calls\\' . ucfirst($this->availableCalls[$task['cid']]['name']);
        } else if (str_starts_with($this->availableCalls[$task['cid']]['package_class'], 'Apps')) {
            $packageClassArr = explode('\\', $this->availableCalls[$task['cid']]['package_class']);
            unset($packageClassArr[$this->helper->lastKey($packageClassArr)]);
            $this->availableCalls[$task['cid']]['package_class'] = implode('\\', $packageClassArr);

            $class = $this->availableCalls[$task['cid']]['package_class'] . '\\TaskCalls\\' . ucfirst($this->availableCalls[$task['cid']]['name']);
        }

        return $class;
    }
}