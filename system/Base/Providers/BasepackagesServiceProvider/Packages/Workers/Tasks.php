<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Workers;

use Carbon\Carbon;
use League\Flysystem\StorageAttributes;
use Phalcon\Helper\Json;
use System\Base\BasePackage;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Workers\BasepackagesWorkersTasks;

class Tasks extends BasePackage
{
    protected $modelToUse = BasepackagesWorkersTasks::class;

    protected $packageName = 'tasks';

    protected $functionsDir = 'system/Base/Providers/BasepackagesServiceProvider/Packages/Workers/Functions/';

    public $tasks;

    public function getFunctionsDir()
    {
        return $this->functionsDir;
    }

    public function init(bool $resetCache = false)
    {
        $this->getAll($resetCache);

        return $this;
    }

    public function getAllFunctions()
    {
        $functionsArr =
            $this->localContent->listContents($this->functionsDir, true)
            ->filter(fn (StorageAttributes $attributes) => $attributes->isFile())
            ->map(fn (StorageAttributes $attributes) => $attributes->path())
            ->toArray();

        $functions = [];

        if (count($functionsArr) > 0) {
            foreach ($functionsArr as $key => $function) {
                $function = ucfirst($function);
                $function = str_replace('/', '\\', $function);
                $function = str_replace('.php', '', $function);

                try {
                    $function = new $function();

                    $functions[$function->packageName]['func'] = $function->packageName;
                    $functions[$function->packageName]['name'] = $function->funcName;

                } catch (\throwable $e) {

                    if ($this->config->logs->exceptions) {
                        $this->logger->logExceptions->debug($e);
                    }
                    continue;
                }
            }
        }

        return $functions;
    }

    public function addTask(array $data)
    {
        if (isset($data['type']) && $data['type'] == 0) {
            $this->addResponse('Cannot add system task.', 1);

            return false;
        }

        if (!isset($data['priority']) || (isset($data['priority']) && $data['priority'] == '0')) {
            $data['priority'] = '1';
        }

        $data['status'] = 0;
        $data['type'] = 1;//1 for user and 0 for system
        $data['previous_run'] = 0;
        $data['next_run'] = 0;

        if ($this->add($data)) {
            $this->addResponse('Added new task ' . $data['name']);
        } else {
            $this->addResponse('Error adding new task', 1);
        }
    }

    public function updateTask(array $data)
    {
        $task = $this->getById($data['id']);

        if (!isset($data['via_job']) &&
            (isset($task['type']) && $task['type'] == 0)
        ) {
            $this->addResponse('Cannot update system task.', 1);

            return false;
        }

        if (!isset($data['priority']) || (isset($data['priority']) && $data['priority'] == '0')) {
            $data['priority'] = '1';
        }

        $task = array_merge($task, $data);

        if ($this->update($task)) {
            $this->addResponse('Updated task ' . $task['name']);
        } else {
            $this->addResponse('Error updating task', 1);
        }
    }

    public function removeTask(array $data)
    {
        $task = $this->getById($data['id']);

        if ($task['type'] == 0) {
            $this->addResponse('Cannot delete system task.', 1);

            return false;
        }

        if ($this->remove($data['id'])) {
            $this->addResponse('Task removed');
        } else {
            $this->addResponse('Error removing task', 1);
        }
    }

    public function forceNextRun(array $data)
    {
        $task = $this->getById($data['id']);

        $task = array_merge($task, $data);

        $time = Carbon::now();

        if (isset($data['cancel']) && $data['cancel'] == 'true') {
            $task['force_next_run'] = null;
            $task['status'] = '1';
            $task['next_run'] = '-';
        } else {
            $task['force_next_run'] = '1';
            $task['status'] = '1';
            $task['next_run'] = $time->addMinute()->startOfMinute()->format('Y-m-d H:i:s');
        }

        if ($this->update($task)) {
            if (isset($data['cancel']) && $data['cancel'] == 'true') {
                $this->addResponse($task['name'] . ' cancelled with worker for next run.');
            } else {
                $this->addResponse($task['name'] . ' scheduled with worker for next run.');
            }
        } else {
            $this->addResponse('Error scheduling task with worker.', 1);
        }
    }

    public function getEnabledTasks()
    {
        if (!$this->tasks) {
            $this->init();
        }

        $sorted = null;

        $taskArr = [];

        foreach ($this->tasks as $taskKey => $task) {
            if (!$task['priority'] || $task['priority'] == '0') {
                $task['priority'] = 1;//Set default priority as we need to sort it later
            }

            if ($task['force_next_run'] == 1) {
                $task['org_schedule_id'] = $task['schedule_id'];
                $task['schedule_id'] = 1;//Make it minute so it can be picked by the scheduler for next run
                array_push($taskArr, $task);
            } else if ($task['enabled'] == 1 && $task['status'] != 2) {//Enabled and not running
                array_push($taskArr, $task);
            }
        }

        $sorted = msort($taskArr, 'priority', SORT_REGULAR, SORT_DESC);

        return $sorted;
    }

    public function findByParameter($parameterValue, $parameterKey = null, $function = null)
    {
        if (!$this->tasks) {
            $this->init();
        }

        foreach ($this->tasks as $taskKey => $task) {
            if ($function && $task['function'] !== $function) {
                continue;
            }

            if (is_string($task['parameters']) && $task['parameters'] !== '') {
                $task['parameters'] = Json::decode($task['parameters'], true);

                if (recursive_array_search($parameterValue, $task['parameters'], $parameterKey)) {
                    return $task;
                }
            }
        }

        return false;
    }
}