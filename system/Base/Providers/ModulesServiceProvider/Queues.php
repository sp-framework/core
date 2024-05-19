<?php

namespace System\Base\Providers\ModulesServiceProvider;

use System\Base\BasePackage;
use System\Base\Providers\ModulesServiceProvider\Model\ServiceProviderModulesQueues;

class Queues extends BasePackage
{
    protected $modelToUse = ServiceProviderModulesQueues::class;

    public $queues;

    public function init(bool $resetCache = false)
    {
        return $this;
    }

    public function getActiveQueue()
    {
        $queue = $this->getFirst('processed', false);

        if (!$queue) {
            return $this->addActiveQueue();
        }

        return $queue->toArray();
    }

    protected function addActiveQueue()
    {
        $queue = [
            'processed' => 0,
            'tasks'     => $this->helper->encode(
                [
                    'install'   => [],
                    'update'    => [],
                    'uninstall' => [],
                    'remove'    => []
                ]
            )
        ];

        if ($this->add($queue)) {
            return $this->packagesData->last;
        }

        throw new \Exception('Cannot add new queue!');
    }

    public function modifyQueue($data)
    {
        if (!isset($data['id']) || !isset($data['moduleType']) || !isset($data['task'])) {
            $this->addResponse('Provide correct information to modify queue', 1);

            return false;
        }

        $queue = $this->getActiveQueue();

        if (!$queue) {
            $this->addResponse('Error retrieving active queue', 1);

            return false;
        }

        $allowedTasks = ['install', 'uninstall', 'update', 'remove', 'cancel', 'clearQueue'];

        if (!in_array($data['task'], $allowedTasks)) {
            $this->addResponse('Unknown task ' . $data['task'], 1);

            return false;
        }

        if ($data['task'] === 'clearQueue') {
            $queue['tasks']['install']   = [];
            $queue['tasks']['update']    = [];
            $queue['tasks']['uninstall'] = [];
            $queue['tasks']['remove']    = [];

            $queue['tasks_count'] = 0;

            $this->addResponse('Queue cleared', 0, ['queue' => $queue]);
        } else if ($data['task'] === 'cancel') {
            $tasksToCheck = ['install', 'update', 'uninstall', 'remove'];

            $found = false;
            foreach ($tasksToCheck as $taskToCheck) {
                if (isset($queue['tasks'][$taskToCheck][$data['moduleType']])) {
                    $key = array_search($data['id'], $queue['tasks'][$taskToCheck][$data['moduleType']]);

                    if ($key !== false) {
                        unset($queue['tasks'][$taskToCheck][$data['moduleType']][$key]);

                        if (count($queue['tasks'][$taskToCheck][$data['moduleType']]) === 0) {
                            unset($queue['tasks'][$taskToCheck][$data['moduleType']]);
                        }

                        $found = true;

                        break;
                    }
                }
            }

            if ($found) {
                $queue['tasks_count'] = $this->getTasksCount($queue);

                $this->addResponse('Removed from queue', 0, ['queue' => $queue]);
            }
        } else {
            if (!isset($queue['tasks'][$data['task']][$data['moduleType']])) {
                $queue['tasks'][$data['task']][$data['moduleType']] = [$data['id']];
            }

            if (!in_array($data['id'], $queue['tasks'][$data['task']][$data['moduleType']])) {
                array_push($queue['tasks'][$data['task']][$data['moduleType']], $data['id']);
            }

            if ($data['task'] === 'install') {
                $tasksToCheck = ['update', 'uninstall', 'remove'];
            } else if ($data['task'] === 'update') {
                $tasksToCheck = ['install', 'uninstall', 'remove'];
            } else if ($data['task'] === 'uninstall') {
                $tasksToCheck = ['install', 'update', 'remove'];
            } else if ($data['task'] === 'remove') {
                $tasksToCheck = ['install', 'update', 'uninstall'];
            }

            foreach ($tasksToCheck as $taskToCheck) {
                if (isset($queue['tasks'][$taskToCheck][$data['moduleType']])) {
                    $key = array_search($data['id'], $queue['tasks'][$taskToCheck][$data['moduleType']]);

                    if ($key !== false) {
                        unset($queue['tasks'][$taskToCheck][$data['moduleType']][$key]);

                        if (count($queue['tasks'][$taskToCheck][$data['moduleType']]) === 0) {
                            unset($queue['tasks'][$taskToCheck][$data['moduleType']]);
                        }
                    }
                }
            }

            $queue['tasks_count'] = $this->getTasksCount($queue);

            $this->addResponse('Added to queue', 0, ['queue' => $queue]);
        }

        if ($this->update($queue)) {
            return true;
        }

        $this->addResponse('Error updating queue', 1);

        return false;
    }

    protected function getTasksCount($queue)
    {
        $counter = 0;

        foreach ($queue['tasks'] as $tasks) {
            if (count($tasks) > 0) {
                foreach ($tasks as $moduleType) {
                    $counter = $counter + count($moduleType);
                }
            }
        }

        return $counter;
    }

    public function analyseActiveQueue()
    {
        $queue = $this->getActiveQueue();

        $queueTasks = [];
        $queueTasksCounter = [];

        foreach ($queue['tasks'] as $taskName => $tasks) {
            if (!isset($queueTasks[$taskName])) {
                $queueTasks[$taskName] = [];
            }

            if (count($tasks) === 0) {
                $queueTasksCounter[$taskName] = 0;

                continue;
            }

            foreach ($tasks as $moduleType => $moduleIds) {
                if (!isset($queueTasks[$taskName][$moduleType])) {
                    $queueTasks[$taskName][$moduleType] = [];
                }

                foreach ($moduleIds as $moduleIdKey => $moduleId) {
                    $moduleMethod = 'get' . ucfirst(substr($moduleType, 0, -1)) . 'ById';
                    $module = $this->modules->$moduleType->$moduleMethod($moduleId);

                    if ($module) {
                        $queueTasks[$taskName][$moduleType][$module['id']] = [];
                        $queueTasks[$taskName][$moduleType][$module['id']]['id'] = $module['id'];
                        $queueTasks[$taskName][$moduleType][$module['id']]['name'] = $module['display_name'] ?? $module['name'];
                        $queueTasks[$taskName][$moduleType][$module['id']]['module_type'] = $module['module_type'];
                        $queueTasks[$taskName][$moduleType][$module['id']]['repo'] = $module['repo'];
                    } else {
                        unset($moduleIds[$moduleIdKey]);
                    }
                }

                $queueTasksCounter[$taskName] = count($queueTasks[$taskName]);
            }
        }

        $this->addResponse('Processed Queue', 0, ['queueTasks' => $queueTasks, 'queueTasksCounter' => $queueTasksCounter]);
    }
}