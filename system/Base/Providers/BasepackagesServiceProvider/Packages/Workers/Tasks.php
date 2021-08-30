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

                } catch (\Exception $e) {
                    throw $e;
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

        $data['status'] = 0;
        $data['type'] = 1;
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
        if (!isset($data['via_job']) &&
            (isset($data['type']) && $data['type'] == 0)
        ) {
            $this->addResponse('Cannot update system task.', 1);

            return false;
        }

        $task = $this->getById($data['id']);

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

        $time = Carbon::now();

        $task['force_next_run'] = '1';
        $task['next_run'] = $time->addMinute()->startOfMinute()->format('Y-m-d H:i:s');

        if ($this->update($task)) {
            $this->addResponse($task['name'] . ' scheduled with worker for next run.');
        } else {
            $this->addResponse('Error scheduling task with worker.', 1);
        }
    }

    public function getEnabledTasks()
    {
        $filter =
            $this->model->filter(
                function($function) {
                    $function = $function->toArray();

                    if ($function['force_next_run'] == 1) {
                        $function['org_schedule_id'] = $function['schedule_id'];
                        $function['schedule_id'] = 1;//Make it minute so it can be picked by the scheduler for next run
                        return $function;
                    } else if ($function['enabled'] == 1) {
                        return $function;
                    }
                }
            );

        $sorted = msort($filter, 'priority', SORT_REGULAR, SORT_DESC);

        return $sorted;
    }
}