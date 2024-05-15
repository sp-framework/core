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

            $this->addResponse('Queue cleared', 0, ['queue' => $queue]);
        } else if ($data['task'] === 'cancel') {
            $tasksToCheck = ['install', 'update', 'uninstall', 'remove'];

            $found = false;
            foreach ($tasksToCheck as $taskToCheck) {
                if (isset($queue['tasks'][$taskToCheck][$data['moduleType']])) {
                    $key = array_search($data['id'], $queue['tasks'][$taskToCheck][$data['moduleType']]);

                    if ($key !== null) {
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

                    if ($key !== null) {
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
}