<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Workers;

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

    public function runTask(int $id)
    {
        //This method will be called via CRON every minute.
        //Taskes that have next_run > current Unix Time will be executed as per their priority.
        //Priorities are set as numbers from 1-10 with 1 being the lowest priority and 10 being the highest.
        //Taskes can be registered to run at certain intervals.
        //Example: Run Email Queue with High priority - Priority set to 10 and process run every minute.
        // $logger = $this->logger;

        // var_dump($this->scheduler);

        // $this->scheduler->call(
        //     function () {
        //         echo 'Iran';
        //     }
        // )->inForeground();

        // $this->scheduler->work([0,10,20,30,40,50]);
    }

    public function getEnabledTasks()
    {
        $filter =
            $this->model->filter(
                function($function) {
                    $function = $function->toArray();

                    if ($function['enabled'] == 1) {
                        return $function;
                    }
                }
            );

        return $filter;
    }
}