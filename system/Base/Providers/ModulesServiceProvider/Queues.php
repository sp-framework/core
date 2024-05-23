<?php

namespace System\Base\Providers\ModulesServiceProvider;

use System\Base\BasePackage;
use System\Base\Providers\ModulesServiceProvider\Model\ServiceProviderModulesQueues;
use z4kn4fein\SemVer\Version;

class Queues extends BasePackage
{
    protected $modelToUse = ServiceProviderModulesQueues::class;

    public $queues;

    protected $queueTasks;

    protected $queueTasksCounter;

    protected $results;

    // protected $coreExternalDependencies;

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
        if ($data['task'] !== 'clearQueue' &&
            (!isset($data['id']) || !isset($data['moduleType']) || !isset($data['task']))
        ) {
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

            $queue['results'] = [];
            $queue['tasks_count'] = [];
            $queue['total'] = 0;
            $queue['status'] = 0;

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

        $this->queueTasks = [];
        $this->queueTasksCounter = [];
        $this->results = [];
        // $this->coreExternalDependencies = $this->getComposerJsonFile();

        foreach ($queue['tasks'] as $taskName => $tasks) {
            if (!isset($this->queueTasks[$taskName])) {
                $this->queueTasks[$taskName] = [];
            }
            if (!isset($this->results[$taskName])) {
                $this->results[$taskName] = [];
            }

            if (count($tasks) === 0) {
                $this->queueTasksCounter[$taskName] = 0;

                continue;
            }

            foreach ($tasks as $moduleType => $moduleIds) {
                if (!isset($this->queueTasks[$taskName][$moduleType])) {
                    $this->queueTasks[$taskName][$moduleType] = [];
                }
                if (!isset($this->results[$taskName][$moduleType])) {
                    $this->results[$taskName][$moduleType] = [];
                }

                foreach ($moduleIds as $moduleIdKey => $moduleId) {
                    $moduleMethod = 'get' . ucfirst(substr($moduleType, 0, -1)) . 'ById';
                    $module = $this->modules->$moduleType->$moduleMethod($moduleId);

                    if (!$module) {
                        unset($queue['tasks'][$taskName][$moduleType][$moduleIdKey]);

                        continue;
                    }

                    if ($moduleType === 'bundles') {
                        if ($taskName === 'remove') {
                            if (!isset($this->queueTasks[$taskName][$moduleType][$module['id']])) {
                                $this->queueTasks[$taskName][$moduleType][$module['id']] = [];
                                $this->queueTasks[$taskName][$moduleType][$module['id']]['id'] = $module['id'];
                                $this->queueTasks[$taskName][$moduleType][$module['id']]['name'] = $module['display_name'] ?? $module['name'];
                                $this->queueTasks[$taskName][$moduleType][$module['id']]['module_type'] = $module['module_type'];
                                $this->queueTasks[$taskName][$moduleType][$module['id']]['version'] = $module['version'];
                                $this->queueTasks[$taskName][$moduleType][$module['id']]['repo'] = $module['repo'];
                                $this->results[$taskName][$moduleType][$module['id']]['analyse'] = 'pass';
                                $this->results[$taskName][$moduleType][$module['id']]['analyse_logs'] = '-';
                                $this->results[$taskName][$moduleType][$module['id']]['precheck'] = '-';
                                $this->results[$taskName][$moduleType][$module['id']]['precheck_logs'] = '-';
                                $this->results[$taskName][$moduleType][$module['id']]['result'] = '-';
                                $this->results[$taskName][$moduleType][$module['id']]['result_logs'] = '-';
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
                                            if (!isset($this->queueTasks[$taskName][$bundleType])) {
                                                $this->queueTasks[$taskName][$bundleType] = [];
                                            }

                                            $bundleModule = $this->apps->types->getAppTypeByRepo($bundles['repo']);

                                            if ($bundleModule) {
                                                $this->queueTasks[$taskName][$bundleType][$bundleModule['id']]['id'] = $bundleModule['id'];
                                                $this->queueTasks[$taskName][$bundleType][$bundleModule['id']]['name'] = $bundleModule['display_name'] ?? $bundleModule['name'];
                                                $this->queueTasks[$taskName][$bundleType][$bundleModule['id']]['module_type'] = 'apptype';
                                                $this->queueTasks[$taskName][$bundleType][$bundleModule['id']]['version'] = $bundleModule['version'];
                                                $this->queueTasks[$taskName][$bundleType][$bundleModule['id']]['repo'] = $bundleModule['repo'];
                                                $this->results[$taskName][$bundleType][$bundleModule['id']]['analyse'] = 'pass';
                                                $this->results[$taskName][$bundleType][$bundleModule['id']]['analyse_logs'] = '-';
                                                $this->results[$taskName][$bundleType][$bundleModule['id']]['precheck'] = '-';
                                                $this->results[$taskName][$bundleType][$bundleModule['id']]['precheck_logs'] = '-';
                                                $this->results[$taskName][$bundleType][$bundleModule['id']]['result'] = '-';
                                                $this->results[$taskName][$bundleType][$bundleModule['id']]['result_logs'] = '-';
                                            } else {
                                                $this->queueTasks[$taskName][$bundleType][0]['id'] = '0';
                                                $this->queueTasks[$taskName][$bundleType][0]['name'] = $bundles['name'];
                                                $this->queueTasks[$taskName][$bundleType][0]['module_type'] = 'apptype';
                                                $this->queueTasks[$taskName][$bundleType][0]['version'] = $bundles['version'];
                                                $this->queueTasks[$taskName][$bundleType][0]['repo'] = $bundles['repo'];
                                                $this->results[$taskName][$bundleType][$bundles['name']]['analyse'] = 'fail';
                                                $this->results[$taskName][$bundleType][$bundles['name']]['analyse_logs'] = $this->getApiClientServices($bundles, true);
                                                $this->results[$taskName][$bundleType][$bundles['name']]['precheck'] = '-';
                                                $this->results[$taskName][$bundleType][$bundles['name']]['precheck_logs'] = '-';
                                                $this->results[$taskName][$bundleType][$bundles['name']]['result'] = '-';
                                                $this->results[$taskName][$bundleType][$bundles['name']]['result_logs'] = '-';
                                            }
                                        } else if ($bundleType === 'core') {
                                            $bundleModule = $this->modules->packages->getPackageByRepo($bundles['repo']);

                                            if ($bundles['version'] !== $bundleModule['version']) {
                                                if (!isset($this->queueTasks['update']['packages'][$bundleModule['id']]['id'])) {
                                                    $this->queueTasks['update']['packages'][$bundleModule['id']]['id'] = $bundleModule['id'];
                                                    $this->queueTasks['update']['packages'][$bundleModule['id']]['name'] = $bundleModule['display_name'] ?? $bundleModule['name'];
                                                    $this->queueTasks['update']['packages'][$bundleModule['id']]['module_type'] = $bundleModule['module_type'];
                                                    $this->queueTasks['update']['packages'][$bundleModule['id']]['version'] = $bundleModule['version'] . ' -> ' . $bundleModule['update_version'];
                                                    $this->queueTasks['update']['packages'][$bundleModule['id']]['repo'] = $bundleModule['repo'];
                                                    $this->results['update']['packages'][$bundleModule['id']]['analyse'] = 'pass';
                                                    $this->results['update']['packages'][$bundleModule['id']]['analyse_logs'] = '-';
                                                    $this->results['update']['packages'][$bundleModule['id']]['precheck'] = '-';
                                                    $this->results['update']['packages'][$bundleModule['id']]['precheck_logs'] = '-';
                                                    $this->results['update']['packages'][$bundleModule['id']]['result'] = '-';
                                                    $this->results['update']['packages'][$bundleModule['id']]['result_logs'] = '-';
                                                }
                                            }
                                        } else {
                                            foreach ($bundles as $bundleKey => $bundle) {
                                                $bundleModuleMethod = 'get' . ucfirst(substr($bundleType, 0, -1)) . 'ByRepo';
                                                $bundleModuleType = $bundleType;

                                                $bundleModule = $this->modules->{$bundleModuleType}->$bundleModuleMethod($bundle['repo']);

                                                if ($bundleModule) {
                                                    if (!isset($this->queueTasks[$taskName][$bundleType][$bundleModule['id']]['id'])) {
                                                        $this->queueTasks[$taskName][$bundleType][$bundleModule['id']]['id'] = $bundleModule['id'];
                                                        $this->queueTasks[$taskName][$bundleType][$bundleModule['id']]['name'] = $bundleModule['display_name'] ?? $bundleModule['name'];
                                                        $this->queueTasks[$taskName][$bundleType][$bundleModule['id']]['module_type'] = $bundleModule['module_type'];
                                                        $this->queueTasks[$taskName][$bundleType][$bundleModule['id']]['version'] = $bundleModule['version'];
                                                        $this->queueTasks[$taskName][$bundleType][$bundleModule['id']]['repo'] = $bundleModule['repo'];
                                                        $this->results[$taskName][$bundleType][$bundleModule['id']]['analyse'] = 'pass';
                                                        $this->results[$taskName][$bundleType][$bundleModule['id']]['analyse_logs'] = '-';
                                                        $this->results[$taskName][$bundleType][$bundleModule['id']]['precheck'] = '-';
                                                        $this->results[$taskName][$bundleType][$bundleModule['id']]['precheck_logs'] = '-';
                                                        $this->results[$taskName][$bundleType][$bundleModule['id']]['result'] = '-';
                                                        $this->results[$taskName][$bundleType][$bundleModule['id']]['result_logs'] = '-';
                                                    }
                                                } else {
                                                    $this->queueTasks[$taskName][$bundleType][$bundleKey]['id'] = '0';
                                                    $this->queueTasks[$taskName][$bundleType][$bundleKey]['name'] = $bundle['name'];
                                                    $this->queueTasks[$taskName][$bundleType][$bundleKey]['module_type'] = $bundleType;
                                                    $this->queueTasks[$taskName][$bundleType][$bundleKey]['version'] = $bundle['version'];
                                                    $this->queueTasks[$taskName][$bundleType][$bundleKey]['repo'] = $bundle['repo'];
                                                    $this->results[$taskName][$bundleType][$bundle['name']]['analyse'] = 'fail';
                                                    $this->results[$taskName][$bundleType][$bundle['name']]['analyse_logs'] = $this->getApiClientServices($bundle, true);
                                                    $this->results[$taskName][$bundleType][$bundle['name']]['precheck'] = '-';
                                                    $this->results[$taskName][$bundleType][$bundle['name']]['precheck_logs'] = '-';
                                                    $this->results[$taskName][$bundleType][$bundle['name']]['result'] = '-';
                                                    $this->results[$taskName][$bundleType][$bundle['name']]['result_logs'] = '-';
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    } else {
                        if (isset($module['dependencies']) && is_string($module['dependencies'])) {
                            $module['dependencies'] = $this->helper->decode($module['dependencies'], true);
                        }

                        foreach ($module['dependencies'] as $dependencyType => $dependencies) {
                            if (count($dependencies) === 0) {
                                continue;
                            }

                            if ($taskName === 'uninstall' || $taskName === 'remove') {
                                continue;//we dont process dependencies for anything other than install/update
                            }

                            if ($dependencyType === 'composer' || $dependencyType === 'external') {
                                continue;
                            }

                            if ($dependencyType === 'core') {
                                $core = $this->modules->packages->getPackageByRepo($dependencies['repo']);

                                if ($dependencies['version'] !== $core['version']) {
                                    if (Version::greaterThan($dependencies['version'], $core['version'])) {
                                        if (isset($core['update_version']) &&
                                            $core['update_version'] !== '' &&
                                            $core['update_version'] === $dependencies['version']
                                        ) {
                                            $this->addToQueueTasksAndResults(
                                                'update',
                                                'packages',
                                                $core
                                            );

                                            // if (!isset($this->queueTasks['update']['packages'][$core['id']]['id'])) {
                                            //     $this->queueTasks['update']['packages'][$core['id']]['id'] = $core['id'];
                                            //     $this->queueTasks['update']['packages'][$core['id']]['name'] = $core['display_name'] ?? $core['name'];
                                            //     $this->queueTasks['update']['packages'][$core['id']]['module_type'] = $core['module_type'];
                                            //     $this->queueTasks['update']['packages'][$core['id']]['version'] = $core['version'] . ' -> ' . $core['update_version'];
                                            //     $this->queueTasks['update']['packages'][$core['id']]['repo'] = $core['repo'];
                                            //     $this->results['update']['packages'][$core['id']]['analyse'] = 'pass';
                                            //     $this->results['update']['packages'][$core['id']]['analyse_logs'] = '-';
                                            //     $this->results['update']['packages'][$core['id']]['precheck'] = '-';
                                            //     $this->results['update']['packages'][$core['id']]['precheck_logs'] = '-';
                                            //     $this->results['update']['packages'][$core['id']]['result'] = '-';
                                            //     $this->results['update']['packages'][$core['id']]['result_logs'] = '-';
                                            // }
                                        } else {
                                            $updateToVersion = $core['version'] . ' -> ' . $core['update_version'];

                                            if (Version::greaterThan($dependencies['version'], $core['update_version'])) {
                                                $updateToVersion = $core['version'] . ' -> ' . $dependencies['version'];
                                            }

                                            $this->addToQueueTasksAndResults(
                                                'update',
                                                'packages',
                                                $core,
                                                $updateToVersion,
                                                'fail',
                                                'Dependencies require version ' . $dependencies['version'] . ' for core. Either the version in dependency is incorrect or you need to sync core repository to get the latest version. If sync does not solve the problem, please contact module developer.'
                                            );

                                            // $this->queueTasks['update']['packages'][$core['id']]['id'] = $core['id'];
                                            // $this->queueTasks['update']['packages'][$core['id']]['name'] = $core['name'];
                                            // $this->queueTasks['update']['packages'][$core['id']]['module_type'] = $core['module_type'];

                                            // if (Version::greaterThan($dependencies['version'], $core['update_version'])) {
                                            //     $this->queueTasks['update']['packages'][$core['id']]['version'] = $core['version'] . ' -> ' . $dependencies['version'];
                                            // } else {
                                            //     $this->queueTasks['update']['packages'][$core['id']]['version'] = $core['version'] . ' -> ' . $core['update_version'];
                                            // }
                                            // $this->queueTasks['update']['packages'][$core['id']]['repo'] = $core['repo'];
                                            // $this->results['update']['packages'][$core['name']]['analyse'] = 'fail';
                                            // $this->results['update']['packages'][$core['name']]['analyse_logs'] = 'Dependencies require version ' . $dependencies['version'] . ' for core. Either the version in dependency is incorrect or you need to sync core repository to get the latest version. If sync does not solve the problem, please contact module developer.';
                                            // $this->results['update']['packages'][$core['name']]['precheck'] = '-';
                                            // $this->results['update']['packages'][$core['name']]['precheck_logs'] = '-';
                                            // $this->results['update']['packages'][$core['name']]['result'] = '-';
                                            // $this->results['update']['packages'][$core['name']]['result_logs'] = '-';
                                        }
                                    }
                                }
                            } else if ($dependencyType === 'apptype') {
                                if (!isset($this->queueTasks[$taskName][$dependencyType])) {
                                    $this->queueTasks[$taskName][$dependencyType] = [];
                                }

                                $appType = $this->apps->types->getAppTypeByRepo($dependencies['repo']);

                                if ($appType) {
                                    if ($dependencies['version'] !== $appType['version']) {
                                        if (Version::greaterThan($dependencies['version'], $appType['version'])) {
                                            if (isset($appType['update_version']) &&
                                                $appType['update_version'] !== ''
                                            ) {
                                                if (Version::greaterThan($dependencies['version'], $appType['update_version'])) {
                                                    $analyseLogs =
                                                        'Dependencies require version ' . $dependencies['version'] . ' for ' . $appType['name'] . '. Either the version in dependency is incorrect or you need to sync ' . $apiClientService['name'] . ' repository to get the latest version. If sync does not solve the problem, please contact module developer.';

                                                    $apiClientService = $this->getApiClientServices($dependencies);
                                                    if (!$apiClientService) {
                                                        $analyseLogs = 'Dependencies require version ' . $dependencies['version'] . ' for ' . $appType['name'] . '. Either the version in dependency is incorrect or you need to re-sync from repository. We also did not find any API client service that can do this. Please add API Client service for the repository and re-sync. If sync does not solve the problem, please contact module developer.';
                                                    }

                                                    $this->addToQueueTasksAndResults(
                                                        $taskName,
                                                        $dependencyType,
                                                        $appType,
                                                        null,
                                                        'fail',
                                                        $analyseLogs
                                                    );
                                                    // $this->queueTasks[$taskName][$dependencyType][$appType['id']]['id'] = '0';
                                                    // $this->queueTasks[$taskName][$dependencyType][$appType['id']]['name'] = $appType['name'];
                                                    // $this->queueTasks[$taskName][$dependencyType][$appType['id']]['module_type'] = $dependencyType;
                                                    // $this->queueTasks[$taskName][$dependencyType][$appType['id']]['version'] = $appType['version'];
                                                    // $this->queueTasks[$taskName][$dependencyType][$appType['id']]['repo'] = $appType['repo'];
                                                    // $this->results[$taskName][$dependencyType][$appType['name']]['analyse'] = 'fail';
                                                    // $apiClientService = $this->getApiClientServices($dependencies);

                                                    // if (!$apiClientService) {
                                                    //     $this->results[$taskName][$dependencyType][$appType['name']]['analyse_logs'] = 'Dependencies require version ' . $dependencies['version'] . ' for ' . $appType['name'] . '. Either the version in dependency is incorrect or you need to re-sync from repository. We also did not find any API client service that can do this. Please add API Client service for the repository and re-sync. If sync does not solve the problem, please contact module developer.';
                                                    // } else {
                                                    //     $this->results[$taskName][$dependencyType][$appType['name']]['analyse_logs'] = 'Dependencies require version ' . $dependencies['version'] . ' for ' . $appType['name'] . '. Either the version in dependency is incorrect or you need to sync ' . $apiClientService['name'] . ' repository to get the latest version. If sync does not solve the problem, please contact module developer.';
                                                    // }
                                                    // $this->results[$taskName][$dependencyType][$appType['name']]['precheck'] = '-';
                                                    // $this->results[$taskName][$dependencyType][$appType['name']]['precheck_logs'] = '-';
                                                    // $this->results[$taskName][$dependencyType][$appType['name']]['result'] = '-';
                                                    // $this->results[$taskName][$dependencyType][$appType['name']]['result_logs'] = '-';
                                                } else if ($appType['update_version'] === $dependencies['version']) {
                                                    if ($appType['installed'] != '1') {
                                                        $this->addToQueueTasksAndResults(
                                                            'install',
                                                            $dependencyType,
                                                            $appType
                                                        );

                                                        // if (!isset($this->queueTasks['install'][$dependencyType][$appType['id']])) {
                                                        //     $this->queueTasks['install'][$dependencyType][$appType['id']] = [];
                                                        //     $this->queueTasks['install'][$dependencyType][$appType['id']]['id'] = $appType['id'];
                                                        //     $this->queueTasks['install'][$dependencyType][$appType['id']]['name'] = $appType['display_name'] ?? $appType['name'];
                                                        //     $this->queueTasks['install'][$dependencyType][$appType['id']]['module_type'] = $dependencyType;
                                                        //     $this->queueTasks['install'][$dependencyType][$appType['id']]['version'] = ($appType['update_version'] ?? $appType['version']);
                                                        //     $this->queueTasks['install'][$dependencyType][$appType['id']]['repo'] = $appType['repo'];
                                                        //     $this->results['install'][$dependencyType][$appType['id']]['analyse'] = 'pass';
                                                        //     $this->results['install'][$dependencyType][$appType['id']]['analyse_logs'] = '-';
                                                        //     $this->results['install'][$dependencyType][$appType['id']]['precheck'] = '-';
                                                        //     $this->results['install'][$dependencyType][$appType['id']]['precheck_logs'] = '-';
                                                        //     $this->results['install'][$dependencyType][$appType['id']]['result'] = '-';
                                                        //     $this->results['install'][$dependencyType][$appType['id']]['result_logs'] = '-';
                                                        // }
                                                    } else {
                                                        $this->addToQueueTasksAndResults(
                                                            'update',
                                                            $dependencyType,
                                                            $appType
                                                        );
                                                        // $this->queueTasks['update'][$dependencyType][$appType['id']] = [];
                                                        // $this->queueTasks['update'][$dependencyType][$appType['id']]['id'] = $appType['id'];
                                                        // $this->queueTasks['update'][$dependencyType][$appType['id']]['name'] = $appType['display_name'] ?? $appType['name'];
                                                        // $this->queueTasks['update'][$dependencyType][$appType['id']]['module_type'] = $dependencyType;
                                                        // $this->queueTasks['update'][$dependencyType][$appType['id']]['version'] = ($appType['update_version'] ?? $appType['version']);
                                                        // $this->queueTasks['update'][$dependencyType][$appType['id']]['repo'] = $appType['repo'];
                                                        // $this->results['update'][$dependencyType][$appType['id']]['analyse'] = 'pass';
                                                        // $this->results['update'][$dependencyType][$appType['id']]['analyse_logs'] = '-';
                                                        // $this->results['update'][$dependencyType][$appType['id']]['precheck'] = '-';
                                                        // $this->results['update'][$dependencyType][$appType['id']]['precheck_logs'] = '-';
                                                        // $this->results['update'][$dependencyType][$appType['id']]['result'] = '-';
                                                        // $this->results['update'][$dependencyType][$appType['id']]['result_logs'] = '-';
                                                    }
                                                }
                                            } else {
                                                $analyseLogs =
                                                    'Dependencies require version ' . $dependencies['version'] . ' for ' . $appType['name'] . '. Either the version in dependency is incorrect or you need to sync ' . $apiClientService['name'] . ' repository to get the latest version. If sync does not solve the problem, please contact module developer.';
                                                $apiClientService = $this->getApiClientServices($dependencies);

                                                if (!$apiClientService) {
                                                    $analyseLogs =
                                                        'Dependencies require version ' . $dependencies['version'] . ' for ' . $appType['name'] . '. Either the version in dependency is incorrect or you need to re-sync from repository. We also did not find any API client service that can do this. Please add API Client service for the repository and re-sync. If sync does not solve the problem, please contact module developer.';
                                                }

                                                $this->addToQueueTasksAndResults(
                                                    $taskName,
                                                    $dependencyType,
                                                    $appType,
                                                    null,
                                                    'fail',
                                                    $analyseLogs
                                                );
                                                // $this->queueTasks[$taskName][$dependencyType][$appType['id']]['id'] = '0';
                                                // $this->queueTasks[$taskName][$dependencyType][$appType['id']]['name'] = $appType['name'];
                                                // $this->queueTasks[$taskName][$dependencyType][$appType['id']]['module_type'] = $dependencyType;
                                                // $this->queueTasks[$taskName][$dependencyType][$appType['id']]['version'] = $appType['version'];
                                                // $this->queueTasks[$taskName][$dependencyType][$appType['id']]['repo'] = $appType['repo'];
                                                // $this->results[$taskName][$dependencyType][$appType['name']]['analyse'] = 'fail';
                                                // $apiClientService = $this->getApiClientServices($dependencies);

                                                // if (!$apiClientService) {
                                                //     $this->results[$taskName][$dependencyType][$appType['name']]['analyse_logs'] = 'Dependencies require version ' . $dependencies['version'] . ' for ' . $appType['name'] . '. Either the version in dependency is incorrect or you need to re-sync from repository. We also did not find any API client service that can do this. Please add API Client service for the repository and re-sync. If sync does not solve the problem, please contact module developer.';
                                                // } else {
                                                //     $this->results[$taskName][$dependencyType][$appType['name']]['analyse_logs'] = 'Dependencies require version ' . $dependencies['version'] . ' for ' . $appType['name'] . '. Either the version in dependency is incorrect or you need to sync ' . $apiClientService['name'] . ' repository to get the latest version. If sync does not solve the problem, please contact module developer.';
                                                // }
                                                // $this->results[$taskName][$dependencyType][$appType['name']]['precheck'] = '-';
                                                // $this->results[$taskName][$dependencyType][$appType['name']]['precheck_logs'] = '-';
                                                // $this->results[$taskName][$dependencyType][$appType['name']]['result'] = '-';
                                                // $this->results[$taskName][$dependencyType][$appType['name']]['result_logs'] = '-';
                                            }
                                        } else {
                                            if ($appType['installed'] != '1') {
                                                $this->addToQueueTasksAndResults(
                                                    'install',
                                                    $dependencyType,
                                                    $appType
                                                );
                                                // if (!isset($this->queueTasks['install'][$dependencyType][$appType['id']])) {
                                                //     $this->queueTasks['install'][$dependencyType][$appType['id']] = [];
                                                //     $this->queueTasks['install'][$dependencyType][$appType['id']]['id'] = $appType['id'];
                                                //     $this->queueTasks['install'][$dependencyType][$appType['id']]['name'] = $appType['display_name'] ?? $appType['name'];
                                                //     $this->queueTasks['install'][$dependencyType][$appType['id']]['module_type'] = $dependencyType;
                                                //     $this->queueTasks['install'][$dependencyType][$appType['id']]['version'] = ($appType['update_version'] ?? $appType['version']);
                                                //     $this->queueTasks['install'][$dependencyType][$appType['id']]['repo'] = $appType['repo'];
                                                //     $this->results['install'][$dependencyType][$appType['id']]['analyse'] = 'pass';
                                                //     $this->results['install'][$dependencyType][$appType['id']]['analyse_logs'] = '-';
                                                //     $this->results['install'][$dependencyType][$appType['id']]['precheck'] = '-';
                                                //     $this->results['install'][$dependencyType][$appType['id']]['precheck_logs'] = '-';
                                                //     $this->results['install'][$dependencyType][$appType['id']]['result'] = '-';
                                                //     $this->results['install'][$dependencyType][$appType['id']]['result_logs'] = '-';
                                                // }
                                            }
                                        }
                                    } else {
                                        if ($appType['installed'] != '1') {
                                            $this->addToQueueTasksAndResults(
                                                'install',
                                                $dependencyType,
                                                $appType
                                            );
                                            // if (!isset($this->queueTasks['install'][$dependencyType][$appType['id']])) {
                                            //     $this->queueTasks['install'][$dependencyType][$appType['id']] = [];
                                            //     $this->queueTasks['install'][$dependencyType][$appType['id']]['id'] = $appType['id'];
                                            //     $this->queueTasks['install'][$dependencyType][$appType['id']]['name'] = $appType['display_name'] ?? $appType['name'];
                                            //     $this->queueTasks['install'][$dependencyType][$appType['id']]['module_type'] = $dependencyType;
                                            //     $this->queueTasks['install'][$dependencyType][$appType['id']]['version'] = ($appType['update_version'] ?? $appType['version']);
                                            //     $this->queueTasks['install'][$dependencyType][$appType['id']]['repo'] = $appType['repo'];
                                            //     $this->results['install'][$dependencyType][$appType['id']]['analyse'] = 'pass';
                                            //     $this->results['install'][$dependencyType][$appType['id']]['analyse_logs'] = '-';
                                            //     $this->results['install'][$dependencyType][$appType['id']]['precheck'] = '-';
                                            //     $this->results['install'][$dependencyType][$appType['id']]['precheck_logs'] = '-';
                                            //     $this->results['install'][$dependencyType][$appType['id']]['result'] = '-';
                                            //     $this->results['install'][$dependencyType][$appType['id']]['result_logs'] = '-';
                                            // }
                                        }
                                    }
                                } else {
                                    $this->addToQueueTasksAndResults(
                                        $taskName,
                                        $dependencyType,
                                        $dependencies,
                                        null,
                                        'fail',
                                        $apiClientService = $this->getApiClientServices($dependencies)
                                    );
                                    // $this->queueTasks[$taskName][$dependencyType][0]['id'] = '0';
                                    // $this->queueTasks[$taskName][$dependencyType][0]['name'] = $dependencies['name'];
                                    // $this->queueTasks[$taskName][$dependencyType][0]['module_type'] = $dependencyType;
                                    // $this->queueTasks[$taskName][$dependencyType][0]['version'] = $dependencies['version'];
                                    // $this->queueTasks[$taskName][$dependencyType][0]['repo'] = $dependencies['repo'];
                                    // $this->results[$taskName][$dependencyType][$dependencies['name']]['analyse'] = 'fail';
                                    // $this->results[$taskName][$dependencyType][$dependencies['name']]['analyse_logs'] = $this->getApiClientServices($dependencies, true);
                                    // $this->results[$taskName][$dependencyType][$dependencies['name']]['precheck'] = '-';
                                    // $this->results[$taskName][$dependencyType][$dependencies['name']]['precheck_logs'] = '-';
                                    // $this->results[$taskName][$dependencyType][$dependencies['name']]['result'] = '-';
                                    // $this->results[$taskName][$dependencyType][$dependencies['name']]['result_logs'] = '-';
                                }
                            } else {
                                if (count($dependencies) > 0) {
                                    foreach ($dependencies as $dependency) {
                                        if (!isset($this->queueTasks[$taskName][$dependencyType])) {
                                            $this->queueTasks[$taskName][$dependencyType] = [];
                                        }

                                        $dependencyModuleMethod = 'get' . ucfirst(substr($dependencyType, 0, -1)) . 'ByRepo';
                                        $dependencyModule = $this->modules->$dependencyType->$dependencyModuleMethod($dependency['repo']);

                                        if ($dependencyModule) {
                                            if ($dependency['version'] !== $dependencyModule['version']) {
                                                if (Version::greaterThan($dependency['version'], $dependencyModule['version'])) {
                                                    if (isset($dependencyModule['update_version']) &&
                                                        $dependencyModule['update_version'] !== ''
                                                    ) {
                                                        if (Version::greaterThan($dependency['version'], $dependencyModule['update_version'])) {
                                                            $analyseLogs = 'Dependencies require version ' . $dependency['version'] . ' for ' . $dependencyModule['name'] . '. Either the version in dependency is incorrect or you need to sync ' . $apiClientService['name'] . ' repository to get the latest version. If sync does not solve the problem, please contact module developer.';
                                                            $apiClientService = $this->getApiClientServices($dependency);
                                                            if (!$apiClientService) {
                                                                $analyseLogs = 'Dependencies require version ' . $dependency['version'] . ' for ' . $dependencyModule['name'] . '. Either the version in dependency is incorrect or you need to re-sync from repository. We also did not find any API client service that can do this. Please add API Client service for the repository and re-sync. If sync does not solve the problem, please contact module developer.';
                                                            }

                                                            $this->addToQueueTasksAndResults(
                                                                $taskName,
                                                                $dependencyType,
                                                                $dependencyModule,
                                                                null,
                                                                'fail',
                                                                $analyseLogs
                                                            );
                                                            // $this->queueTasks[$taskName][$dependencyType][$dependencyModule['id']]['id'] = '0';
                                                            // $this->queueTasks[$taskName][$dependencyType][$dependencyModule['id']]['name'] = $dependencyModule['name'];
                                                            // $this->queueTasks[$taskName][$dependencyType][$dependencyModule['id']]['module_type'] = $dependencyType;
                                                            // $this->queueTasks[$taskName][$dependencyType][$dependencyModule['id']]['version'] = $dependencyModule['version'];
                                                            // $this->queueTasks[$taskName][$dependencyType][$dependencyModule['id']]['repo'] = $dependencyModule['repo'];
                                                            // $this->results[$taskName][$dependencyType][$dependencyModule['name']]['analyse'] = 'fail';
                                                            // $apiClientService = $this->getApiClientServices($dependency);
                                                            // if (!$apiClientService) {
                                                            //     $this->results[$taskName][$dependencyType][$dependencyModule['name']]['analyse_logs'] = 'Dependencies require version ' . $dependency['version'] . ' for ' . $dependencyModule['name'] . '. Either the version in dependency is incorrect or you need to re-sync from repository. We also did not find any API client service that can do this. Please add API Client service for the repository and re-sync. If sync does not solve the problem, please contact module developer.';
                                                            // } else {
                                                            //     $this->results[$taskName][$dependencyType][$dependencyModule['name']]['analyse_logs'] = 'Dependencies require version ' . $dependency['version'] . ' for ' . $dependencyModule['name'] . '. Either the version in dependency is incorrect or you need to sync ' . $apiClientService['name'] . ' repository to get the latest version. If sync does not solve the problem, please contact module developer.';
                                                            // }
                                                            // $this->results[$taskName][$dependencyType][$dependencyModule['name']]['precheck'] = '-';
                                                            // $this->results[$taskName][$dependencyType][$dependencyModule['name']]['precheck_logs'] = '-';
                                                            // $this->results[$taskName][$dependencyType][$dependencyModule['name']]['result'] = '-';
                                                            // $this->results[$taskName][$dependencyType][$dependencyModule['name']]['result_logs'] = '-';
                                                        } else if ($dependencyModule['update_version'] === $dependency['version']) {
                                                            if ($dependencyModule['installed'] != '1') {
                                                                $this->addToQueueTasksAndResults(
                                                                    'install',
                                                                    $dependencyType,
                                                                    $dependencyModule
                                                                );
                                                                if ($dependencyType === 'views' &&
                                                                    array_key_exists('is_subview', $dependencyModule) &&
                                                                    $dependencyModule['is_subview'] == 0
                                                                ) {
                                                                    $dependencyModule['id'] = $dependencyModule['id'] . '-public';
                                                                    $dependencyModule['name'] = ($dependencyModule['display_name'] ?? $dependencyModule['name']) . ' (Public)';
                                                                    $dependencyModule['repo'] = $dependencyModule['repo'] . '-public';
                                                                    $this->addToQueueTasksAndResults(
                                                                        'install',
                                                                        $dependencyType,
                                                                        $dependencyModule
                                                                    );
                                                                }

                                                                // if (!isset($this->queueTasks['install'][$dependencyType][$dependencyModule['id']])) {
                                                                    // $this->queueTasks['install'][$dependencyType][$dependencyModule['id']] = [];
                                                                    // $this->queueTasks['install'][$dependencyType][$dependencyModule['id']]['id'] = $dependencyModule['id'];
                                                                    // $this->queueTasks['install'][$dependencyType][$dependencyModule['id']]['name'] = $dependencyModule['display_name'] ?? $dependencyModule['name'];
                                                                    // $this->queueTasks['install'][$dependencyType][$dependencyModule['id']]['module_type'] = $dependencyType;
                                                                    // $this->queueTasks['install'][$dependencyType][$dependencyModule['id']]['version'] = ($dependencyModule['update_version'] ?? $dependencyModule['version']);
                                                                    // $this->queueTasks['install'][$dependencyType][$dependencyModule['id']]['repo'] = $dependencyModule['repo'];
                                                                    // $this->results['install'][$dependencyType][$dependencyModule['id']]['analyse'] = 'pass';
                                                                    // $this->results['install'][$dependencyType][$dependencyModule['id']]['analyse_logs'] = '-';
                                                                    // $this->results['install'][$dependencyType][$dependencyModule['id']]['precheck'] = '-';
                                                                    // $this->results['install'][$dependencyType][$dependencyModule['id']]['precheck_logs'] = '-';
                                                                    // $this->results['install'][$dependencyType][$dependencyModule['id']]['result'] = '-';
                                                                    // $this->results['install'][$dependencyType][$dependencyModule['id']]['result_logs'] = '-';
                                                                    // if ($dependencyType === 'views') {
                                                                    //     if (array_key_exists('is_subview', $dependencyModule) && $dependencyModule['is_subview'] == 0) {
                                                                    //         $this->queueTasks['install'][$dependencyType][$dependencyModule['id'] . '-public'] = [];
                                                                    //         $this->queueTasks['install'][$dependencyType][$dependencyModule['id'] . '-public']['id'] = $dependencyModule['id'];
                                                                    //         $this->queueTasks['install'][$dependencyType][$dependencyModule['id'] . '-public']['name'] = ($dependencyModule['display_name'] ?? $dependencyModule['name']) . ' (Public)';
                                                                    //         $this->queueTasks['install'][$dependencyType][$dependencyModule['id'] . '-public']['module_type'] = $dependencyType;
                                                                    //         $this->queueTasks['install'][$dependencyType][$dependencyModule['id'] . '-public']['version'] = $dependencyModule['update_version'] ?? $dependencyModule['version'];
                                                                    //         $this->queueTasks['install'][$dependencyType][$dependencyModule['id'] . '-public']['repo'] = $dependencyModule['repo'] . '-public';
                                                                    //         $this->results['install'][$dependencyType][$dependencyModule['id'] . '-public']['analyse'] = 'pass';
                                                                    //         $this->results['install'][$dependencyType][$dependencyModule['id'] . '-public']['analyse_logs'] = '-';
                                                                    //         $this->results['install'][$dependencyType][$dependencyModule['id'] . '-public']['precheck'] = '-';
                                                                    //         $this->results['install'][$dependencyType][$dependencyModule['id'] . '-public']['precheck_logs'] = '-';
                                                                    //         $this->results['install'][$dependencyType][$dependencyModule['id'] . '-public']['result'] = '-';
                                                                    //         $this->results['install'][$dependencyType][$dependencyModule['id'] . '-public']['result_logs'] = '-';
                                                                    //     }
                                                                    // }
                                                                // }
                                                            } else {
                                                                $this->addToQueueTasksAndResults(
                                                                    'update',
                                                                    $dependencyType,
                                                                    $dependencyModule
                                                                );
                                                                if ($dependencyType === 'views' &&
                                                                    array_key_exists('is_subview', $dependencyModule) &&
                                                                    $dependencyModule['is_subview'] == 0
                                                                ) {
                                                                    $dependencyModule['id'] = $dependencyModule['id'] . '-public';
                                                                    $dependencyModule['name'] = ($dependencyModule['display_name'] ?? $dependencyModule['name']) . ' (Public)';
                                                                    $dependencyModule['repo'] = $dependencyModule['repo'] . '-public';
                                                                    $this->addToQueueTasksAndResults(
                                                                        'update',
                                                                        $dependencyType,
                                                                        $dependencyModule
                                                                    );
                                                                }
                                                                // $this->queueTasks['update'][$dependencyType][$dependencyModule['id']] = [];
                                                                // $this->queueTasks['update'][$dependencyType][$dependencyModule['id']]['id'] = $dependencyModule['id'];
                                                                // $this->queueTasks['update'][$dependencyType][$dependencyModule['id']]['name'] = $dependencyModule['display_name'] ?? $dependencyModule['name'];
                                                                // $this->queueTasks['update'][$dependencyType][$dependencyModule['id']]['module_type'] = $dependencyType;
                                                                // $this->queueTasks['update'][$dependencyType][$dependencyModule['id']]['version'] = ($dependencyModule['update_version'] ?? $dependencyModule['version']);
                                                                // $this->queueTasks['update'][$dependencyType][$dependencyModule['id']]['repo'] = $dependencyModule['repo'];
                                                                // $this->results['update'][$dependencyType][$dependencyModule['id']]['analyse'] = 'pass';
                                                                // $this->results['update'][$dependencyType][$dependencyModule['id']]['analyse_logs'] = '-';
                                                                // $this->results['update'][$dependencyType][$dependencyModule['id']]['precheck'] = '-';
                                                                // $this->results['update'][$dependencyType][$dependencyModule['id']]['precheck_logs'] = '-';
                                                                // $this->results['update'][$dependencyType][$dependencyModule['id']]['result'] = '-';
                                                                // $this->results['update'][$dependencyType][$dependencyModule['id']]['result_logs'] = '-';
                                                                // if ($dependencyType === 'views') {
                                                                //     if (array_key_exists('is_subview', $dependencyModule) && $dependencyModule['is_subview'] == 0) {
                                                                //         $this->queueTasks['update'][$dependencyType][$dependencyModule['id'] . '-public'] = [];
                                                                //         $this->queueTasks['update'][$dependencyType][$dependencyModule['id'] . '-public']['id'] = $dependencyModule['id'];
                                                                //         $this->queueTasks['update'][$dependencyType][$dependencyModule['id'] . '-public']['name'] = ($dependencyModule['display_name'] ?? $dependencyModule['name']) . ' (Public)';
                                                                //         $this->queueTasks['update'][$dependencyType][$dependencyModule['id'] . '-public']['module_type'] = $dependencyType;
                                                                //         $this->queueTasks['update'][$dependencyType][$dependencyModule['id'] . '-public']['version'] = ($dependencyModule['update_version'] && $dependencyModule['update_version'] !== '') ? $dependencyModule['version'] . ' -> ' . $dependencyModule['update_version'] : $dependencyModule['version'];
                                                                //         $this->queueTasks['update'][$dependencyType][$dependencyModule['id'] . '-public']['repo'] = $dependencyModule['repo'] . '-public';
                                                                //         $this->results['update'][$dependencyType][$dependencyModule['id'] . '-public']['analyse'] = 'pass';
                                                                //         $this->results['update'][$dependencyType][$dependencyModule['id'] . '-public']['analyse_logs'] = '-';
                                                                //         $this->results['update'][$dependencyType][$dependencyModule['id'] . '-public']['precheck'] = '-';
                                                                //         $this->results['update'][$dependencyType][$dependencyModule['id'] . '-public']['precheck_logs'] = '-';
                                                                //         $this->results['update'][$dependencyType][$dependencyModule['id'] . '-public']['result'] = '-';
                                                                //         $this->results['update'][$dependencyType][$dependencyModule['id'] . '-public']['result_logs'] = '-';
                                                                //     }
                                                                // }
                                                            }
                                                        }
                                                    } else {
                                                        $analyseLogs = 'Dependencies require version ' . $dependency['version'] . ' for ' . $dependencyModule['name'] . '. Either the version in dependency is incorrect or you need to sync ' . $apiClientService['name'] . ' repository to get the latest version. If sync does not solve the problem, please contact module developer.';

                                                        $apiClientService = $this->getApiClientServices($dependency);
                                                        if (!$apiClientService) {
                                                            $analyseLogs = 'Dependencies require version ' . $dependency['version'] . ' for ' . $dependencyModule['name'] . '. Either the version in dependency is incorrect or you need to re-sync from repository. We also did not find any API client service that can do this. Please add API Client service for the repository and re-sync. If sync does not solve the problem, please contact module developer.';
                                                        }

                                                        $this->addToQueueTasksAndResults(
                                                            $taskName,
                                                            $dependencyType,
                                                            $dependencyModule,
                                                            null,
                                                            'fail',
                                                            $analyseLogs
                                                        );
                                                        // $this->queueTasks[$taskName][$dependencyType][$dependencyModule['id']]['id'] = '0';
                                                        // $this->queueTasks[$taskName][$dependencyType][$dependencyModule['id']]['name'] = $dependencyModule['name'];
                                                        // $this->queueTasks[$taskName][$dependencyType][$dependencyModule['id']]['module_type'] = $dependencyType;
                                                        // $this->queueTasks[$taskName][$dependencyType][$dependencyModule['id']]['version'] = $dependencyModule['version'];
                                                        // $this->queueTasks[$taskName][$dependencyType][$dependencyModule['id']]['repo'] = $dependencyModule['repo'];
                                                        // $this->results[$taskName][$dependencyType][$dependencyModule['name']]['analyse'] = 'fail';
                                                        // $apiClientService = $this->getApiClientServices($dependency);

                                                        // if (!$apiClientService) {
                                                        //     $this->results[$taskName][$dependencyType][$dependencyModule['name']]['analyse_logs'] = 'Dependencies require version ' . $dependency['version'] . ' for ' . $dependencyModule['name'] . '. Either the version in dependency is incorrect or you need to re-sync from repository. We also did not find any API client service that can do this. Please add API Client service for the repository and re-sync. If sync does not solve the problem, please contact module developer.';
                                                        // } else {
                                                        //     $this->results[$taskName][$dependencyType][$dependencyModule['name']]['analyse_logs'] = 'Dependencies require version ' . $dependency['version'] . ' for ' . $dependencyModule['name'] . '. Either the version in dependency is incorrect or you need to sync ' . $apiClientService['name'] . ' repository to get the latest version. If sync does not solve the problem, please contact module developer.';
                                                        // }
                                                        // $this->results[$taskName][$dependencyType][$dependencyModule['name']]['precheck'] = '-';
                                                        // $this->results[$taskName][$dependencyType][$dependencyModule['name']]['precheck_logs'] = '-';
                                                        // $this->results[$taskName][$dependencyType][$dependencyModule['name']]['result'] = '-';
                                                        // $this->results[$taskName][$dependencyType][$dependencyModule['name']]['result_logs'] = '-';
                                                    }
                                                } else {
                                                    if ($dependencyModule['installed'] != '1') {
                                                        $this->addToQueueTasksAndResults(
                                                            'install',
                                                            $dependencyType,
                                                            $dependencyModule
                                                        );
                                                        if ($dependencyType === 'views' &&
                                                            array_key_exists('is_subview', $dependencyModule) &&
                                                            $dependencyModule['is_subview'] == 0
                                                        ) {
                                                            $dependencyModule['id'] = $dependencyModule['id'] . '-public';
                                                            $dependencyModule['name'] = ($dependencyModule['display_name'] ?? $dependencyModule['name']) . ' (Public)';
                                                            $dependencyModule['repo'] = $dependencyModule['repo'] . '-public';
                                                            $this->addToQueueTasksAndResults(
                                                                'install',
                                                                $dependencyType,
                                                                $dependencyModule
                                                            );
                                                        }
                                                        // if (!isset($this->queueTasks['install'][$dependencyType][$dependencyModule['id']])) {
                                                        //     $this->queueTasks['install'][$dependencyType][$dependencyModule['id']] = [];
                                                        //     $this->queueTasks['install'][$dependencyType][$dependencyModule['id']]['id'] = $dependencyModule['id'];
                                                        //     $this->queueTasks['install'][$dependencyType][$dependencyModule['id']]['name'] = $dependencyModule['display_name'] ?? $dependencyModule['name'];
                                                        //     $this->queueTasks['install'][$dependencyType][$dependencyModule['id']]['module_type'] = $dependencyType;
                                                        //     $this->queueTasks['install'][$dependencyType][$dependencyModule['id']]['version'] = $dependencyModule['update_version'] ?? $dependencyModule['version'];
                                                        //     $this->queueTasks['install'][$dependencyType][$dependencyModule['id']]['repo'] = $dependencyModule['repo'];
                                                        //     $this->results['install'][$dependencyType][$dependencyModule['id']]['analyse'] = 'pass';
                                                        //     $this->results['install'][$dependencyType][$dependencyModule['id']]['analyse_logs'] = '-';
                                                        //     $this->results['install'][$dependencyType][$dependencyModule['id']]['precheck'] = '-';
                                                        //     $this->results['install'][$dependencyType][$dependencyModule['id']]['precheck_logs'] = '-';
                                                        //     $this->results['install'][$dependencyType][$dependencyModule['id']]['result'] = '-';
                                                        //     $this->results['install'][$dependencyType][$dependencyModule['id']]['result_logs'] = '-';
                                                        //     if ($dependencyType === 'views') {
                                                        //         if (array_key_exists('is_subview', $dependencyModule) && $dependencyModule['is_subview'] == 0) {
                                                        //             $this->queueTasks['install'][$dependencyType][$dependencyModule['id'] . '-public'] = [];
                                                        //             $this->queueTasks['install'][$dependencyType][$dependencyModule['id'] . '-public']['id'] = $dependencyModule['id'];
                                                        //             $this->queueTasks['install'][$dependencyType][$dependencyModule['id'] . '-public']['name'] = ($dependencyModule['display_name'] ?? $dependencyModule['name']) . ' (Public)';
                                                        //             $this->queueTasks['install'][$dependencyType][$dependencyModule['id'] . '-public']['module_type'] = $dependencyType;
                                                        //             $this->queueTasks['install'][$dependencyType][$dependencyModule['id'] . '-public']['version'] = $dependencyModule['update_version'] ?? $dependencyModule['version'];
                                                        //             $this->queueTasks['install'][$dependencyType][$dependencyModule['id'] . '-public']['repo'] = $dependencyModule['repo'] . '-public';
                                                        //             $this->results['install'][$dependencyType][$dependencyModule['id'] . '-public']['analyse'] = 'pass';
                                                        //             $this->results['install'][$dependencyType][$dependencyModule['id'] . '-public']['analyse_logs'] = '-';
                                                        //             $this->results['install'][$dependencyType][$dependencyModule['id'] . '-public']['precheck'] = '-';
                                                        //             $this->results['install'][$dependencyType][$dependencyModule['id'] . '-public']['precheck_logs'] = '-';
                                                        //             $this->results['install'][$dependencyType][$dependencyModule['id'] . '-public']['result'] = '-';
                                                        //             $this->results['install'][$dependencyType][$dependencyModule['id'] . '-public']['result_logs'] = '-';
                                                        //         }
                                                        //     }
                                                        // }
                                                    }
                                                }
                                            } else {
                                                if ($dependencyModule['installed'] != '1') {
                                                    $this->addToQueueTasksAndResults(
                                                        'install',
                                                        $dependencyType,
                                                        $dependencyModule
                                                    );
                                                    if ($dependencyType === 'views' &&
                                                        array_key_exists('is_subview', $dependencyModule) &&
                                                        $dependencyModule['is_subview'] == 0
                                                    ) {
                                                        $dependencyModule['id'] = $dependencyModule['id'] . '-public';
                                                        $dependencyModule['name'] = ($dependencyModule['display_name'] ?? $dependencyModule['name']) . ' (Public)';
                                                        $dependencyModule['repo'] = $dependencyModule['repo'] . '-public';
                                                        $this->addToQueueTasksAndResults(
                                                            'install',
                                                            $dependencyType,
                                                            $dependencyModule
                                                        );
                                                    }
                                                    // if (!isset($this->queueTasks['install'][$dependencyType][$dependencyModule['id']])) {
                                                    //     $this->queueTasks['install'][$dependencyType][$dependencyModule['id']] = [];
                                                    //     $this->queueTasks['install'][$dependencyType][$dependencyModule['id']]['id'] = $dependencyModule['id'];
                                                    //     $this->queueTasks['install'][$dependencyType][$dependencyModule['id']]['name'] = $dependencyModule['display_name'] ?? $dependencyModule['name'];
                                                    //     $this->queueTasks['install'][$dependencyType][$dependencyModule['id']]['module_type'] = $dependencyType;
                                                    //     $this->queueTasks['install'][$dependencyType][$dependencyModule['id']]['version'] = $dependencyModule['update_version'] ?? $dependencyModule['version'];
                                                    //     $this->queueTasks['install'][$dependencyType][$dependencyModule['id']]['repo'] = $dependencyModule['repo'];
                                                    //     $this->results['install'][$dependencyType][$dependencyModule['id']]['analyse'] = 'pass';
                                                    //     $this->results['install'][$dependencyType][$dependencyModule['id']]['analyse_logs'] = '-';
                                                    //     $this->results['install'][$dependencyType][$dependencyModule['id']]['precheck'] = '-';
                                                    //     $this->results['install'][$dependencyType][$dependencyModule['id']]['precheck_logs'] = '-';
                                                    //     $this->results['install'][$dependencyType][$dependencyModule['id']]['result'] = '-';
                                                    //     $this->results['install'][$dependencyType][$dependencyModule['id']]['result_logs'] = '-';
                                                    //     if ($dependencyType === 'views') {
                                                    //         if (array_key_exists('is_subview', $dependencyModule) && $dependencyModule['is_subview'] == 0) {
                                                    //             $this->queueTasks['install'][$dependencyType][$dependencyModule['id'] . '-public'] = [];
                                                    //             $this->queueTasks['install'][$dependencyType][$dependencyModule['id'] . '-public']['id'] = $dependencyModule['id'];
                                                    //             $this->queueTasks['install'][$dependencyType][$dependencyModule['id'] . '-public']['name'] = ($dependencyModule['display_name'] ?? $dependencyModule['name']) . ' (Public)';
                                                    //             $this->queueTasks['install'][$dependencyType][$dependencyModule['id'] . '-public']['module_type'] = $dependencyType;
                                                    //             $this->queueTasks['install'][$dependencyType][$dependencyModule['id'] . '-public']['version'] = $dependencyModule['update_version'] ?? $dependencyModule['version'];
                                                    //             $this->queueTasks['install'][$dependencyType][$dependencyModule['id'] . '-public']['repo'] = $dependencyModule['repo'] . '-public';
                                                    //             $this->results['install'][$dependencyType][$dependencyModule['id'] . '-public']['analyse'] = 'pass';
                                                    //             $this->results['install'][$dependencyType][$dependencyModule['id'] . '-public']['analyse_logs'] = '-';
                                                    //             $this->results['install'][$dependencyType][$dependencyModule['id'] . '-public']['precheck'] = '-';
                                                    //             $this->results['install'][$dependencyType][$dependencyModule['id'] . '-public']['precheck_logs'] = '-';
                                                    //             $this->results['install'][$dependencyType][$dependencyModule['id'] . '-public']['result'] = '-';
                                                    //             $this->results['install'][$dependencyType][$dependencyModule['id'] . '-public']['result_logs'] = '-';
                                                    //         }
                                                    //     }
                                                    // }
                                                }
                                            }
                                        } else {
                                            $this->queueTasks[$taskName][$dependencyType][0]['id'] = '0';
                                            $this->queueTasks[$taskName][$dependencyType][0]['name'] = $dependency['name'];
                                            $this->queueTasks[$taskName][$dependencyType][0]['module_type'] = $dependencyType;
                                            $this->queueTasks[$taskName][$dependencyType][0]['version'] = $dependency['version'];
                                            $this->queueTasks[$taskName][$dependencyType][0]['repo'] = $dependency['repo'];
                                            $this->results[$taskName][$dependencyType][$dependency['name']]['analyse'] = 'fail';
                                            $this->results[$taskName][$dependencyType][$dependency['name']]['analyse_logs'] = $this->getApiClientServices($dependency, true);
                                            $this->results[$taskName][$dependencyType][$dependency['name']]['precheck'] = '-';
                                            $this->results[$taskName][$dependencyType][$dependency['name']]['precheck_logs'] = '-';
                                            $this->results[$taskName][$dependencyType][$dependency['name']]['result'] = '-';
                                            $this->results[$taskName][$dependencyType][$dependency['name']]['result_logs'] = '-';
                                        }
                                    }
                                }

                            // } else {Composer dependencies will be resolved during pre-check to make sure we have also downloaded any patches that needs to be added
                            //
                                // foreach ($dependencies['require'] as $dependencyPackage => $dependencyVersion) {
                                //     $this->coreExternalDependencies['require'][$dependencyPackage] = $dependencyVersion;
                                // }

                                // if (isset($dependencies['config'])) {
                                //     $this->coreExternalDependencies['config'] = array_merge_recursive_distinct($this->coreExternalDependencies['config'], $dependencies['config']);
                                // }

                                // if (isset($dependencies['extra'])) {
                                //     if (isset($dependencies['extra']['patches']) && count($dependencies['extra']['patches']) > 0) {
                                //         foreach ($dependencies['extra']['patches'] as &$patches) {
                                //             if (count($patches) > 0) {
                                //                 foreach ($patches as $patchKey => &$patch) {
                                //                     //Check if the patch actually exists!!
                                //                     $patch = base_path($patch);
                                //
                                //                 }
                                //             }
                                //         }
                                //     }
                                //     $this->coreExternalDependencies['extra'] = array_merge_recursive_distinct($this->coreExternalDependencies['extra'], $dependencies['extra']);
                                // }

                                // try {
                                //     $this->localContent->write('external/composer.json', $this->helper->encode($this->coreExternalDependencies, JSON_PRETTY_PRINT));
                                // } catch (\throwable $exception) {

                                // }
                            }
                        }

                        // if ($taskName === 'update') {
                        //     if (isset($module['update_version']) &&
                        //         $module['update_version'] !== ''
                        //     ) {
                        //         $module['version'] = $module['version'] . ' -> ' . $module['update_version'];
                        //     }
                        // }

                        $this->addToQueueTasksAndResults(
                            $taskName,
                            $moduleType,
                            $module
                        );
                        if ($moduleType === 'views' &&
                            array_key_exists('is_subview', $module) &&
                            $module['is_subview'] == 0
                        ) {
                            $module['id'] = $module['id'] . '-public';
                            $module['name'] = ($module['display_name'] ?? $module['name']) . ' (Public)';
                            $module['repo'] = $module['repo'] . '-public';

                            $this->addToQueueTasksAndResults(
                                $taskName,
                                $moduleType,
                                $module
                            );
                        }

                        // if (!isset($this->queueTasks[$taskName][$moduleType][$module['id']])) {
                        //     $this->queueTasks[$taskName][$moduleType][$module['id']] = [];
                        //     $this->queueTasks[$taskName][$moduleType][$module['id']]['id'] = $module['id'];
                        //     $this->queueTasks[$taskName][$moduleType][$module['id']]['name'] = $module['display_name'] ?? $module['name'];
                        //     $this->queueTasks[$taskName][$moduleType][$module['id']]['module_type'] = $module['module_type'];
                        //     $this->queueTasks[$taskName][$moduleType][$module['id']]['version'] = $module['version'];
                        //     if ($taskName === 'update') {
                        //         $this->queueTasks[$taskName][$moduleType][$module['id']]['version'] = ($module['update_version'] && $module['update_version'] !== '') ? $module['version'] . ' -> ' . $module['update_version'] : $module['version'];
                        //     }
                        //     $this->queueTasks[$taskName][$moduleType][$module['id']]['repo'] = $module['repo'];
                        //     $this->results[$taskName][$moduleType][$module['id']]['analyse'] = 'pass';
                        //     $this->results[$taskName][$moduleType][$module['id']]['analyse_logs'] = '-';
                        //     $this->results[$taskName][$moduleType][$module['id']]['precheck'] = '-';
                        //     $this->results[$taskName][$moduleType][$module['id']]['precheck_logs'] = '-';
                        //     $this->results[$taskName][$moduleType][$module['id']]['result'] = '-';
                        //     $this->results[$taskName][$moduleType][$module['id']]['result_logs'] = '-';
                        //     if ($moduleType === 'views') {
                        //         if (array_key_exists('is_subview', $module) && $module['is_subview'] == 0) {
                        //             $this->queueTasks[$taskName][$moduleType][$module['id'] . '-public'] = [];
                        //             $this->queueTasks[$taskName][$moduleType][$module['id'] . '-public']['id'] = $module['id'];
                        //             $this->queueTasks[$taskName][$moduleType][$module['id'] . '-public']['name'] = ($module['display_name'] ?? $module['name']) . ' (Public)';
                        //             $this->queueTasks[$taskName][$moduleType][$module['id'] . '-public']['module_type'] = $moduleType;
                        //             $this->queueTasks[$taskName][$moduleType][$module['id'] . '-public']['version'] = $module['version'];
                        //             if ($taskName === 'update') {
                        //                 $this->queueTasks[$taskName][$moduleType][$module['id'] . '-public']['version'] = ($module['update_version'] && $module['update_version'] !== '') ? $module['version'] . ' -> ' . $module['update_version'] : $module['version'];
                        //             }
                        //             $this->queueTasks[$taskName][$moduleType][$module['id'] . '-public']['repo'] = $module['repo'] . '-public';
                        //             $this->results[$taskName][$moduleType][$module['id'] . '-public']['analyse'] = 'pass';
                        //             $this->results[$taskName][$moduleType][$module['id'] . '-public']['analyse_logs'] = '-';
                        //             $this->results[$taskName][$moduleType][$module['id'] . '-public']['precheck'] = '-';
                        //             $this->results[$taskName][$moduleType][$module['id'] . '-public']['precheck_logs'] = '-';
                        //             $this->results[$taskName][$moduleType][$module['id'] . '-public']['result'] = '-';
                        //             $this->results[$taskName][$moduleType][$module['id'] . '-public']['result_logs'] = '-';
                        //         }
                        //     }
                        // }
                    }
                }

                $this->queueTasksCounter[$taskName] = count($this->queueTasks[$taskName]);
            }
        }

        $queue['tasks_count'] = $this->queueTasksCounter;
        $queue['results'] = $this->results;
        trace([$queue, $this->queueTasks, $this->results]);
        if ($this->update($queue)) {
            $this->addResponse('Analysed Queue', 0, ['queueTasks' => $this->queueTasks, 'queueTasksCounter' => $this->queueTasksCounter]);

            return true;
        }

        $this->addResponse('Error analysing queue', 1);

        return false;
    }

    protected function getApiClientServices($module, $getLogMessage = false)
    {
        $appTypeRepo = explode('/', str_replace('https://', '', $module['repo']));

        unset($appTypeRepo[$this->helper->lastKey($appTypeRepo)]);

        $appTypeRepo = 'https://' . implode('/', $appTypeRepo);

        $apiClientService = $this->basepackages->apiClientServices->getApiByRepoUrl($appTypeRepo);

        if ($getLogMessage) {
            if (!$apiClientService) {
                $logMessage = 'No API client configured for retrieving module from ' . $module['repo'] . ' location';
            } else {
                $logMessage = 'Module ' . ($module['display_name'] ?? $module['name']) . ' not found locally. Please sync API client service ' . $apiClientService['name'];
            }

            return $logMessage;
        }

        return $apiClientService;
    }

    protected function addToQueueTasksAndResults(
        $taskName,
        $moduleType,
        $module,
        $version = null,
        $analyseResult = 'pass',
        $analyseResultLogs = '-',
    )
    {
        $moduleId = $module['id'];

        if ($analyseResult === 'fail') {
            $moduleId = '0';
            $module['id'] = $module['name'];
        }

        if (!isset($this->queueTasks[$taskName][$moduleType][$module['id']])) {
            $this->queueTasks[$taskName][$moduleType][$module['id']] = [];
            $this->queueTasks[$taskName][$moduleType][$module['id']]['id'] = $moduleId;
            $this->queueTasks[$taskName][$moduleType][$module['id']]['name'] = $module['display_name'] ?? $module['name'];
            $this->queueTasks[$taskName][$moduleType][$module['id']]['module_type'] = $moduleType;
            if (!$version) {
                $this->queueTasks[$taskName][$moduleType][$module['id']]['version'] = ($taskName === 'update' && $module['update_version'] && $module['update_version'] !== '') ? $module['version'] . ' -> ' . $module['update_version'] : $module['version'];
            } else {
                $this->queueTasks[$taskName][$moduleType][$module['id']]['version'] = $version;
            }
            $this->queueTasks[$taskName][$moduleType][$module['id']]['repo'] = $module['repo'];
            $this->results[$taskName][$moduleType][$module['id']]['analyse'] = 'pass';
            $this->results[$taskName][$moduleType][$module['id']]['analyse_logs'] = '-';
            $this->results[$taskName][$moduleType][$module['id']]['precheck'] = '-';
            $this->results[$taskName][$moduleType][$module['id']]['precheck_logs'] = '-';
            $this->results[$taskName][$moduleType][$module['id']]['result'] = '-';
            $this->results[$taskName][$moduleType][$module['id']]['result_logs'] = '-';
        }
    }

    protected function getComposerJsonFile()
    {
        if (file_exists(base_path('external/composer.lock'))) {
            unlink(base_path('external/composer.lock'));
        }

        try {
            return $this->helper->decode($this->localContent->read('external/composer.json'), true);
        } catch (\throwable $exception) {
            return false;
        }
    }
}