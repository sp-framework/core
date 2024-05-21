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
        $queue = $this->getFirst('status', 0);

        if (!$queue) {
            return $this->addActiveQueue();
        }

        $queue = $queue->toArray();

        return $queue;
    }

    protected function addActiveQueue()
    {
        $queue = [
            'status' => 0,//1 - pre-checked, 2 - processed
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
            $queue = $this->packagesData->last;

            $queue['total'] = 0;

            return $queue;
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

            $queue['tasks_count'] = [];
            $queue['total'] = 0;

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
                $this->getTasksCount($queue);

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

            $this->getTasksCount($queue);

            $this->addResponse('Added to queue', 0, ['queue' => $queue]);
        }

        $queue['prechecked_at'] = null;
        $queue['prechecked_by'] = null;

        if ($this->update($queue)) {
            return true;
        }

        $this->addResponse('Error updating queue', 1);

        return false;
    }

    protected function getTasksCount(&$queue)
    {
        $queue['total'] = 0;
        $queue['tasks_count'] = [];

        foreach ($queue['tasks'] as $taskType => $task) {
            if (count($task) > 0) {
                foreach ($task as $moduleType => $modules) {
                    if (count($modules) > 0) {
                        if (isset($queue['tasks_count'][$taskType]) && $queue['tasks_count'][$taskType] > 0) {
                            $queue['tasks_count'][$taskType] = $queue['tasks_count'][$taskType] + count($modules);
                        } else {
                            $queue['tasks_count'][$taskType] = count($modules);
                        }
                    }
                }

                $queue['total'] = $queue['total'] + $queue['tasks_count'][$taskType];
            }
        }
    }

    public function analyseQueue(&$queue = null)
    {
        if (!$queue) {
            $queue = $this->getActiveQueue();
        }

        $queueTasks = [];
        $queueTasksCounter = [];
        $results = [];

        foreach ($queue['tasks'] as $taskName => $tasks) {
            if (!isset($queueTasks[$taskName])) {
                $queueTasks[$taskName] = [];
            }
            if (!isset($results[$taskName])) {
                $results[$taskName] = [];
            }

            if (count($tasks) === 0) {
                $queueTasksCounter[$taskName] = 0;

                continue;
            }

            foreach ($tasks as $moduleType => $moduleIds) {
                if (!isset($queueTasks[$taskName][$moduleType])) {
                    $queueTasks[$taskName][$moduleType] = [];
                }
                if (!isset($results[$taskName][$moduleType])) {
                    $results[$taskName][$moduleType] = [];
                }

                foreach ($moduleIds as $moduleIdKey => $moduleId) {
                    $moduleMethod = 'get' . ucfirst(substr($moduleType, 0, -1)) . 'ById';
                    $module = $this->modules->$moduleType->$moduleMethod($moduleId);

                    if ($moduleType === 'bundles') {
                        if ($taskName === 'remove') {
                            if (!isset($queueTasks[$taskName][$moduleType][$module['id']])) {
                                $queueTasks[$taskName][$moduleType][$module['id']] = [];
                                $queueTasks[$taskName][$moduleType][$module['id']]['id'] = $module['id'];
                                $queueTasks[$taskName][$moduleType][$module['id']]['name'] = $module['display_name'] ?? $module['name'];
                                $queueTasks[$taskName][$moduleType][$module['id']]['module_type'] = $module['module_type'];
                                $queueTasks[$taskName][$moduleType][$module['id']]['version'] = $module['version'];
                                $queueTasks[$taskName][$moduleType][$module['id']]['repo'] = $module['repo'];
                                $results[$taskName][$moduleType][$module['id']]['analyse'] = 'pass';
                                $results[$taskName][$moduleType][$module['id']]['analyse_errors'] = '-';
                                $results[$taskName][$moduleType][$module['id']]['precheck'] = '-';
                                $results[$taskName][$moduleType][$module['id']]['precheck_errors'] = '-';
                                $results[$taskName][$moduleType][$module['id']]['result'] = '-';
                                $results[$taskName][$moduleType][$module['id']]['result_errors'] = '-';
                            }
                        } else {
                            if (isset($module['bundle_modules'])) {
                                if (is_string($module['bundle_modules'])) {
                                    $module['bundle_modules'] = $this->helper->decode($module['bundle_modules'], true);
                                }

                                if (count($module['bundle_modules']) > 0) {
                                    foreach ($module['bundle_modules'] as $bundleType => $bundles) {
                                        if (count($bundles) === 0) {
                                            continue;
                                        }

                                        if ($bundleType === 'apptype') {
                                            if (!isset($queueTasks[$taskName][$bundleType])) {
                                                $queueTasks[$taskName][$bundleType] = [];
                                            }
                                            $bundleModule = $this->apps->types->getAppTypeByRepo($bundles['repo']);
                                            if ($bundleModule) {
                                                $queueTasks[$taskName][$bundleType][$bundleModule['id']]['id'] = $bundleModule['id'];
                                                $queueTasks[$taskName][$bundleType][$bundleModule['id']]['name'] = $bundleModule['display_name'] ?? $bundleModule['name'];
                                                $queueTasks[$taskName][$bundleType][$bundleModule['id']]['module_type'] = 'apptype';
                                                $queueTasks[$taskName][$bundleType][$bundleModule['id']]['version'] = $bundleModule['version'];
                                                $queueTasks[$taskName][$bundleType][$bundleModule['id']]['repo'] = $bundleModule['repo'];
                                                $results[$taskName][$bundleType][$bundleModule['id']]['analyse'] = 'pass';
                                                $results[$taskName][$bundleType][$bundleModule['id']]['analyse_errors'] = '-';
                                                $results[$taskName][$bundleType][$bundleModule['id']]['precheck'] = '-';
                                                $results[$taskName][$bundleType][$bundleModule['id']]['precheck_errors'] = '-';
                                                $results[$taskName][$bundleType][$bundleModule['id']]['result'] = '-';
                                                $results[$taskName][$bundleType][$bundleModule['id']]['result_errors'] = '-';

                                            } else {
                                                $queueTasks[$taskName][$bundleType][0]['id'] = '0';
                                                $queueTasks[$taskName][$bundleType][0]['name'] = $bundles['name'];
                                                $queueTasks[$taskName][$bundleType][0]['module_type'] = 'apptype';
                                                $queueTasks[$taskName][$bundleType][0]['version'] = $bundles['version'];
                                                $queueTasks[$taskName][$bundleType][0]['repo'] = $bundles['repo'];
                                                $results[$taskName][$bundleType][$bundles['name']]['analyse'] = 'fail';
                                                $results[$taskName][$bundleType][$bundles['name']]['analyse_errors'] = 'Bundle module not found in local system.';
                                                $results[$taskName][$bundleType][$bundles['name']]['precheck'] = '-';
                                                $results[$taskName][$bundleType][$bundles['name']]['precheck_errors'] = '-';
                                                $results[$taskName][$bundleType][$bundles['name']]['result'] = '-';
                                                $results[$taskName][$bundleType][$bundles['name']]['result_errors'] = '-';
                                            }
                                        } else if ($bundleType === 'core') {
                                            $bundleModule = $this->modules->packages->getPackageByRepo($bundles['repo']);

                                            if ($bundles['version'] !== $bundleModule['version']) {
                                                if (!isset($queueTasks['update']['packages'][$bundleModule['id']]['id'])) {
                                                    $queueTasks['update']['packages'][$bundleModule['id']]['id'] = $bundleModule['id'];
                                                    $queueTasks['update']['packages'][$bundleModule['id']]['name'] = $bundleModule['display_name'] ?? $bundleModule['name'];
                                                    $queueTasks['update']['packages'][$bundleModule['id']]['module_type'] = $bundleModule['module_type'];
                                                    $queueTasks['update']['packages'][$bundleModule['id']]['version'] = $bundleModule['version'] . ' -> ' . $bundleModule['update_version'];
                                                    $queueTasks['update']['packages'][$bundleModule['id']]['repo'] = $bundleModule['repo'];
                                                    $results['update']['packages'][$bundleModule['id']]['analyse'] = 'pass';
                                                    $results['update']['packages'][$bundleModule['id']]['analyse_errors'] = '-';
                                                    $results['update']['packages'][$bundleModule['id']]['precheck'] = '-';
                                                    $results['update']['packages'][$bundleModule['id']]['precheck_errors'] = '-';
                                                    $results['update']['packages'][$bundleModule['id']]['result'] = '-';
                                                    $results['update']['packages'][$bundleModule['id']]['result_errors'] = '-';
                                                }
                                            }
                                        } else {
                                            foreach ($bundles as $bundleKey => $bundle) {
                                                $bundleModuleMethod = 'get' . ucfirst(substr($bundleType, 0, -1)) . 'ByRepo';
                                                $bundleModuleType = $bundleType;

                                                $bundleModule = $this->modules->{$bundleModuleType}->$bundleModuleMethod($bundle['repo']);

                                                if ($bundleModule) {
                                                    if (!isset($queueTasks[$taskName][$bundleType][$bundleModule['id']]['id'])) {
                                                        $queueTasks[$taskName][$bundleType][$bundleModule['id']]['id'] = $bundleModule['id'];
                                                        $queueTasks[$taskName][$bundleType][$bundleModule['id']]['name'] = $bundleModule['display_name'] ?? $bundleModule['name'];
                                                        $queueTasks[$taskName][$bundleType][$bundleModule['id']]['module_type'] = $bundleModule['module_type'];
                                                        $queueTasks[$taskName][$bundleType][$bundleModule['id']]['version'] = $bundleModule['version'];
                                                        $queueTasks[$taskName][$bundleType][$bundleModule['id']]['repo'] = $bundleModule['repo'];
                                                        $results[$taskName][$bundleType][$bundleModule['id']]['analyse'] = 'pass';
                                                        $results[$taskName][$bundleType][$bundleModule['id']]['analyse_errors'] = '-';
                                                        $results[$taskName][$bundleType][$bundleModule['id']]['precheck'] = '-';
                                                        $results[$taskName][$bundleType][$bundleModule['id']]['precheck_errors'] = '-';
                                                        $results[$taskName][$bundleType][$bundleModule['id']]['result'] = '-';
                                                        $results[$taskName][$bundleType][$bundleModule['id']]['result_errors'] = '-';
                                                    }
                                                } else {
                                                    $queueTasks[$taskName][$bundleType][$bundleKey]['id'] = '0';
                                                    $queueTasks[$taskName][$bundleType][$bundleKey]['name'] = $bundle['name'];
                                                    $queueTasks[$taskName][$bundleType][$bundleKey]['module_type'] = $bundleType;
                                                    $queueTasks[$taskName][$bundleType][$bundleKey]['version'] = $bundle['version'];
                                                    $queueTasks[$taskName][$bundleType][$bundleKey]['repo'] = $bundle['repo'];
                                                    $results[$taskName][$bundleType][$bundle['name']]['analyse'] = 'fail';
                                                    $results[$taskName][$bundleType][$bundle['name']]['analyse_errors'] = 'Bundle module not found in local system.';
                                                    $results[$taskName][$bundleType][$bundle['name']]['precheck'] = '-';
                                                    $results[$taskName][$bundleType][$bundle['name']]['precheck_errors'] = '-';
                                                    $results[$taskName][$bundleType][$bundle['name']]['result'] = '-';
                                                    $results[$taskName][$bundleType][$bundle['name']]['result_errors'] = '-';
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    } else {
                        if (!isset($queueTasks[$taskName][$moduleType][$module['id']])) {
                            $queueTasks[$taskName][$moduleType][$module['id']] = [];
                            $queueTasks[$taskName][$moduleType][$module['id']]['id'] = $module['id'];
                            $queueTasks[$taskName][$moduleType][$module['id']]['name'] = $module['display_name'] ?? $module['name'];
                            $queueTasks[$taskName][$moduleType][$module['id']]['module_type'] = $module['module_type'];
                            $queueTasks[$taskName][$moduleType][$module['id']]['version'] = $module['version'];
                            if ($taskName === 'update') {
                                $queueTasks[$taskName][$moduleType][$module['id']]['version'] = $module['version'] . ' -> ' . $module['update_version'];
                            }
                            $queueTasks[$taskName][$moduleType][$module['id']]['repo'] = $module['repo'];
                            $results[$taskName][$moduleType][$module['id']]['analyse'] = 'pass';
                            $results[$taskName][$moduleType][$module['id']]['analyse_errors'] = '-';
                            $results[$taskName][$moduleType][$module['id']]['precheck'] = '-';
                            $results[$taskName][$moduleType][$module['id']]['precheck_errors'] = '-';
                            $results[$taskName][$moduleType][$module['id']]['result'] = '-';
                            $results[$taskName][$moduleType][$module['id']]['result_errors'] = '-';
                        }
                    }
                }

                $queueTasksCounter[$taskName] = count($queueTasks[$taskName]);
            }
        }

        $queue['tasks_count'] = $queueTasksCounter;
        $queue['results'] = $results;

        if ($this->update($queue)) {
            $this->addResponse('Analysed Queue', 0, ['queueTasks' => $queueTasks, 'queueTasksCounter' => $queueTasksCounter]);

            return true;
        }

        $this->addResponse('Error analysing queue', 1);

        return false;
    }
}