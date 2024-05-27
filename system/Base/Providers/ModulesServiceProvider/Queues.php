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
        $params['conditions'] = '';
        $params['limit'] = 1;
        $params['order'] = 'id desc';

        $oldQueue = $this->getByParams($params);

        if ($oldQueue && count($oldQueue) === 1) {
            $oldQueue = $oldQueue[0];
        }

        $queue = [
            'status'    => 0,//1 - pre-checked, 2 - processed
            'tasks'     => $this->helper->encode(
                [
                    'install'   => [],
                    'update'    => [],
                    'uninstall' => [],
                    'remove'    => []
                ]
            ),
            'total'     => 0
        ];

        if ($oldQueue && isset($oldQueue['sync'])) {
            $queue['sync'] = $oldQueue['sync'];
        }

        if ($this->add($queue)) {
            $queue = $this->packagesData->last;

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

        $allowedTasks = ['update', 'install', 'uninstall', 'remove', 'cancel', 'clearQueue'];

        if (!in_array($data['task'], $allowedTasks)) {
            $this->addResponse('Unknown task ' . $data['task'], 1);

            return false;
        }

        if ($data['task'] === 'clearQueue') {
            $queue['tasks']['update']    = [];
            $queue['tasks']['install']   = [];
            $queue['tasks']['uninstall'] = [];
            $queue['tasks']['remove']    = [];

            $queue['results'] = [];
            $queue['tasks_count'] = [];
            $queue['total'] = 0;
            $queue['status'] = 0;

            $this->addResponse('Queue cleared', 0, ['queue' => $queue]);
        } else if ($data['task'] === 'cancel') {
            $tasksToCheck = ['update', 'install', 'uninstall', 'remove'];

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

            if ($data['task'] === 'update') {
                $tasksToCheck = ['install', 'uninstall', 'remove'];
            } else if ($data['task'] === 'install') {
                $tasksToCheck = ['update', 'uninstall', 'remove'];
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

    protected function getTasksCount(&$queue, $analyse = false)
    {
        $queue['total'] = 0;
        $queue['tasks_count'] = [];
        $tasks = $queue['tasks'];
        if ($analyse) {
            $tasks = $queue['results'];
        }
        foreach ($tasks as $taskType => $task) {
            if (isset($tasks['analysed']) &&
                $taskType === 'analysed'
            ) {
                continue;
            }

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
        $this->results = [];
        // $this->coreExternalDependencies = $this->getComposerJsonFile();

        foreach ($queue['tasks'] as $taskName => $tasks) {
            if (isset($queue['tasks']['analysed']) &&
                $taskName === 'analysed'
            ) {
                continue;
            }

            if (!isset($this->queueTasks[$taskName])) {
                $this->queueTasks[$taskName] = [];
            }
            if (!isset($this->results[$taskName])) {
                $this->results[$taskName] = [];
            }

            if (count($tasks) === 0) {
                continue;
            }

            foreach ($tasks as $moduleType => $moduleIds) {
                if (!isset($this->queueTasks[$taskName][$moduleType]) &&
                    $moduleType !== 'bundles'
                ) {
                    $this->queueTasks[$taskName][$moduleType] = [];
                }
                if (!isset($this->results[$taskName][$moduleType]) &&
                    $moduleType !== 'bundles'
                ) {
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
                            $this->addToQueueTasksAndResults($taskName, $moduleType, $module);
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
                                            $bundleModule = $this->apps->types->getAppTypeByRepo($bundles['repo']);

                                            if ($bundleModule) {
                                                $this->compareAndAddToQueue($bundles, $bundleModule, $taskName, $bundleType);
                                                // $this->addToQueueTasksAndResults($taskName, $bundleType, $bundleModule);
                                            } else {
                                                $this->addToQueueTasksAndResults($taskName, $bundleType, $bundles, null, 'fail', $this->getApiClientServices($bundles, true));
                                            }
                                        } else if ($bundleType === 'core') {
                                            $bundleModule = $this->modules->packages->getPackageByRepo($bundles['repo']);

                                            $this->compareAndAddToQueue($bundles, $bundleModule, 'first', 'packages');
                                        } else {
                                            if ($bundleType === 'external') {
                                                $this->checkComposerAndAddToQueue($bundles);

                                                continue;
                                            }

                                            foreach ($bundles as $bundleKey => $bundle) {
                                                $bundleModuleMethod = 'get' . ucfirst(substr($bundleType, 0, -1)) . 'ByRepo';
                                                $bundleModuleType = $bundleType;

                                                $bundleModule = $this->modules->{$bundleModuleType}->$bundleModuleMethod($bundle['repo']);

                                                if ($bundleModule) {
                                                    $this->compareAndAddToQueue($bundle, $bundleModule, $taskName, $bundleType);
                                                    // $this->addToQueueTasksAndResults($taskName, $bundleType, $bundleModule);

                                                    // if ($bundleType === 'views' &&
                                                    //     array_key_exists('is_subview', $bundleModule) &&
                                                    //     $bundleModule['is_subview'] == 0
                                                    // ) {
                                                    //     $bundleModule['id'] = $bundleModule['id'] . '-public';
                                                    //     $bundleModule['name'] = ($bundleModule['display_name'] ?? $bundleModule['name']) . ' (Public)';
                                                    //     $bundleModule['repo'] = $bundleModule['repo'] . '-public';

                                                    //     $this->addToQueueTasksAndResults('install', $bundleType, $bundleModule);
                                                    // }
                                                } else {
                                                    $this->addToQueueTasksAndResults($taskName, $bundleType, $bundle, null, 'fail', $this->getApiClientServices($bundle, true));
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
                                $this->checkComposerAndAddToQueue($dependencies);

                                continue;
                            }

                            if ($dependencyType === 'core') {
                                $core = $this->modules->packages->getPackageByRepo($dependencies['repo']);

                                $this->compareAndAddToQueue($dependencies, $core, 'first', 'packages');
                                // if (Version::greaterThan($dependencies['version'], $core['version'])) {
                                //     if (isset($core['update_version']) &&
                                //         $core['update_version'] !== ''
                                //     ) {
                                //         if (Version::greaterThan($dependencies['version'], $core['update_version'])) {
                                //             $apiClientService = $this->getApiClientServices($dependencies);
                                //             if ($apiClientService) {
                                //                 $analyseLogs = 'Dependencies require version ' . $dependencies['version'] . ' for ' . $core['name'] . '. Either the version in dependency is incorrect or you need to sync ' . $apiClientService['name'] . ' repository to get the latest version. If sync does not solve the problem, please contact module developer.';
                                //             } else {
                                //                 $analyseLogs = 'Dependencies require version ' . $dependency['version'] . ' for ' . $core['name'] . '. Either the version in dependency is incorrect or you need to re-sync from repository. We also did not find any API client service that can do this. Please add API Client service for the repository and re-sync. If sync does not solve the problem, please contact module developer.';
                                //             }

                                //             $this->addToQueueTasksAndResults('update', 'packages', $core, null, 'fail', $analyseLogs);
                                //         } else if (Version::equal($dependencies['version'], $core['update_version'])) {
                                //             $this->addToQueueTasksAndResults('update', 'packages', $core);
                                //         }
                                //     } else {
                                //         $apiClientService = $this->getApiClientServices($dependencies);
                                //         if ($apiClientService) {
                                //             $analyseLogs = 'Dependencies require version ' . $dependencies['version'] . ' for ' . $core['name'] . '. Either the version in dependency is incorrect or you need to sync ' . $apiClientService['name'] . ' repository to get the latest version. If sync does not solve the problem, please contact module developer.';
                                //         } else {
                                //             $analyseLogs = 'Dependencies require version ' . $dependencies['version'] . ' for ' . $core['name'] . '. Either the version in dependency is incorrect or you need to re-sync from repository. We also did not find any API client service that can do this. Please add API Client service for the repository and re-sync. If sync does not solve the problem, please contact module developer.';
                                //         }

                                //         $this->addToQueueTasksAndResults('update', 'packages', $core, null, 'fail', $analyseLogs);
                                //     }
                                // }
                            } else if ($dependencyType === 'apptype') {
                                $appType = $this->apps->types->getAppTypeByRepo($dependencies['repo']);

                                if ($appType) {
                                    $this->compareAndAddToQueue($dependencies, $appType, $taskName, $dependencyType);
                                    // if (Version::greaterThan($dependencies['version'], $appType['version'])) {
                                    //     if (isset($appType['update_version']) &&
                                    //         $appType['update_version'] !== ''
                                    //     ) {
                                    //         if (Version::greaterThan($dependencies['version'], $appType['update_version'])) {
                                    //             $apiClientService = $this->getApiClientServices($dependencies);
                                    //             if ($apiClientService) {
                                    //                 $analyseLogs =
                                    //                     'Dependencies require version ' . $dependencies['version'] . ' for ' . $appType['name'] . '. Either the version in dependency is incorrect or you need to sync ' . $apiClientService['name'] . ' repository to get the latest version. If sync does not solve the problem, please contact module developer.';
                                    //             } else {
                                    //                 $analyseLogs = 'Dependencies require version ' . $dependencies['version'] . ' for ' . $appType['name'] . '. Either the version in dependency is incorrect or you need to re-sync from repository. We also did not find any API client service that can do this. Please add API Client service for the repository and re-sync. If sync does not solve the problem, please contact module developer.';
                                    //             }

                                    //             $this->addToQueueTasksAndResults($taskName, $dependencyType, $appType, null, 'fail', $analyseLogs);
                                    //         } else if (Version::equal($dependencies['version'], $appType['update_version'])) {
                                    //             if ($appType['installed'] != '1') {
                                    //                 $this->addToQueueTasksAndResults('install', $dependencyType, $appType);
                                    //             } else {
                                    //                 $this->addToQueueTasksAndResults('update', $dependencyType, $appType);
                                    //             }
                                    //         }
                                    //     } else {
                                    //         $apiClientService = $this->getApiClientServices($dependencies);
                                    //         if ($apiClientService) {
                                    //             $analyseLogs =
                                    //                 'Dependencies require version ' . $dependencies['version'] . ' for ' . $appType['name'] . '. Either the version in dependency is incorrect or you need to sync ' . $apiClientService['name'] . ' repository to get the latest version. If sync does not solve the problem, please contact module developer.';
                                    //         } else {
                                    //             $analyseLogs =
                                    //                 'Dependencies require version ' . $dependencies['version'] . ' for ' . $appType['name'] . '. Either the version in dependency is incorrect or you need to re-sync from repository. We also did not find any API client service that can do this. Please add API Client service for the repository and re-sync. If sync does not solve the problem, please contact module developer.';
                                    //         }

                                    //         $this->addToQueueTasksAndResults($taskName, $dependencyType, $appType, null, 'fail', $analyseLogs);
                                    //     }
                                    // } else {
                                    //     if ($appType['installed'] != '1') {
                                    //         $this->addToQueueTasksAndResults('install', $dependencyType, $appType);
                                    //     }
                                    // }
                                } else {
                                    $this->addToQueueTasksAndResults($taskName, $dependencyType, $dependencies, null, 'fail', $this->getApiClientServices($dependencies, true));
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
                                            $removeUninstallArr = ['remove', 'uninstall'];

                                            foreach ($removeUninstallArr as $removeUninstall) {
                                                if (isset($queue['tasks'][$removeUninstall][$dependencyType]) && in_array($dependencyModule['id'], $queue['tasks'][$removeUninstall][$dependencyType])) {
                                                    $this->addToQueueTasksAndResults($taskName, $dependencyType, $dependencyModule, null, 'fail', 'Dependency is in ' . $removeUninstall . ' task, but is also required by module <strong>' . ($module['display_name'] ?? $module['name']) . '</strong>');

                                                    continue 2;
                                                }
                                            }

                                            $this->compareAndAddToQueue($dependency, $dependencyModule, $taskName, $dependencyType);
                                            // if (Version::greaterThan($dependency['version'], $dependencyModule['version'])) {
                                            //     if (isset($dependencyModule['update_version']) &&
                                            //         $dependencyModule['update_version'] !== ''
                                            //     ) {
                                            //         if (Version::greaterThan($dependency['version'], $dependencyModule['update_version'])) {
                                            //             $apiClientService = $this->getApiClientServices($dependency);
                                            //             if ($apiClientService) {
                                            //                 $analyseLogs = 'Dependencies require version ' . $dependency['version'] . ' for ' . $dependencyModule['name'] . '. Either the version in dependency is incorrect or you need to sync ' . $apiClientService['name'] . ' repository to get the latest version. If sync does not solve the problem, please contact module developer.';
                                            //             } else {
                                            //                 $analyseLogs = 'Dependencies require version ' . $dependency['version'] . ' for ' . $dependencyModule['name'] . '. Either the version in dependency is incorrect or you need to re-sync from repository. We also did not find any API client service that can do this. Please add API Client service for the repository and re-sync. If sync does not solve the problem, please contact module developer.';
                                            //             }

                                            //             $this->addToQueueTasksAndResults($taskName, $dependencyType, $dependencyModule, null, 'fail', $analyseLogs);
                                            //         } else if (Version::equal($dependency['version'], $dependencyModule['update_version'])) {
                                            //             if ($dependencyModule['installed'] != '1') {
                                            //                 $this->addToQueueTasksAndResults('install', $dependencyType, $dependencyModule);

                                            //                 if ($dependencyType === 'views' &&
                                            //                     array_key_exists('is_subview', $dependencyModule) &&
                                            //                     $dependencyModule['is_subview'] == 0
                                            //                 ) {
                                            //                     $dependencyModule['id'] = $dependencyModule['id'] . '-public';
                                            //                     $dependencyModule['name'] = ($dependencyModule['display_name'] ?? $dependencyModule['name']) . ' (Public)';
                                            //                     $dependencyModule['repo'] = $dependencyModule['repo'] . '-public';

                                            //                     $this->addToQueueTasksAndResults('install', $dependencyType, $dependencyModule);
                                            //                 }
                                            //             } else {
                                            //                 $this->addToQueueTasksAndResults('update', $dependencyType, $dependencyModule);

                                            //                 if ($dependencyType === 'views' &&
                                            //                     array_key_exists('is_subview', $dependencyModule) &&
                                            //                     $dependencyModule['is_subview'] == 0
                                            //                 ) {
                                            //                     $dependencyModule['id'] = $dependencyModule['id'] . '-public';
                                            //                     $dependencyModule['name'] = ($dependencyModule['display_name'] ?? $dependencyModule['name']) . ' (Public)';
                                            //                     $dependencyModule['repo'] = $dependencyModule['repo'] . '-public';

                                            //                     $this->addToQueueTasksAndResults('update', $dependencyType, $dependencyModule);
                                            //                 }
                                            //             }
                                            //         }
                                            //     } else {
                                            //         $apiClientService = $this->getApiClientServices($dependency);
                                            //         if ($apiClientService) {
                                            //             $analyseLogs = 'Dependencies require version ' . $dependency['version'] . ' for ' . $dependencyModule['name'] . '. Either the version in dependency is incorrect or you need to sync ' . $apiClientService['name'] . ' repository to get the latest version. If sync does not solve the problem, please contact module developer.';
                                            //         } else {
                                            //             $analyseLogs = 'Dependencies require version ' . $dependency['version'] . ' for ' . $dependencyModule['name'] . '. Either the version in dependency is incorrect or you need to re-sync from repository. We also did not find any API client service that can do this. Please add API Client service for the repository and re-sync. If sync does not solve the problem, please contact module developer.';
                                            //         }

                                            //         $this->addToQueueTasksAndResults($taskName, $dependencyType, $dependencyModule, null, 'fail', $analyseLogs);
                                            //     }
                                            // } else {
                                            //     if ($dependencyModule['installed'] != '1') {
                                            //         $this->addToQueueTasksAndResults('install', $dependencyType, $dependencyModule);

                                            //         if ($dependencyType === 'views' &&
                                            //             array_key_exists('is_subview', $dependencyModule) &&
                                            //             $dependencyModule['is_subview'] == 0
                                            //         ) {
                                            //             $dependencyModule['id'] = $dependencyModule['id'] . '-public';
                                            //             $dependencyModule['name'] = ($dependencyModule['display_name'] ?? $dependencyModule['name']) . ' (Public)';
                                            //             $dependencyModule['repo'] = $dependencyModule['repo'] . '-public';

                                            //             $this->addToQueueTasksAndResults('install', $dependencyType, $dependencyModule);
                                            //         }
                                            //     }
                                            // }
                                        } else {
                                            $this->addToQueueTasksAndResults($taskName, $dependencyType, $dependency, null, 'fail', $this->getApiClientServices($dependency, true));
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

                        $module['name'] = $module['display_name'] ?? $module['name'];
                        $this->addToQueueTasksAndResults($taskName, $moduleType, $module);

                        if ($moduleType === 'views' &&
                            array_key_exists('is_subview', $module) &&
                            $module['is_subview'] == 0
                        ) {
                            $module['id'] = $module['id'] . '-public';
                            $module['name'] = ($module['display_name'] ?? $module['name']) . ' (Public)';
                            $module['repo'] = $module['repo'] . '-public';

                            $this->addToQueueTasksAndResults($taskName, $moduleType, $module);
                        }
                    }
                }
            }
        }

        $queue['results'] = $this->results;
        //Rearrange tasks to make sure we install externals first and then update core and then execute rest of the tasks.
        if (isset($this->queueTasks['first'])) {
            $this->queueTasks = array_merge(array_flip(['first', 'update', 'install', 'uninstall', 'remove']), $this->queueTasks);
            $this->queueTasks['first'] = array_merge(array_flip(['external', 'packages']), $this->queueTasks['first']);
        }

        $queue['tasks']['analysed'] = $this->queueTasks;

        $this->getTasksCount($queue, true);

        if ($this->update($queue)) {
            $this->addResponse('Analysed Queue');

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

    protected function compareAndAddToQueue($requestedModule, $installedModule, $task, $moduleType)
    {
        if (Version::greaterThan($requestedModule['version'], $installedModule['version'])) {
            if (isset($installedModule['update_version']) &&
                $installedModule['update_version'] !== ''
            ) {
                if (Version::greaterThan($requestedModule['version'], $installedModule['update_version'])) {
                    $apiClientService = $this->getApiClientServices($requestedModule);
                    if ($apiClientService) {
                        $analyseLogs = 'Dependencies require version ' . $requestedModule['version'] . ' for ' . $installedModule['name'] . '. Either the version in dependency is incorrect or you need to sync ' . $apiClientService['name'] . ' repository to get the latest version. If sync does not solve the problem, please contact module developer.';
                    } else {
                        $analyseLogs = 'Dependencies require version ' . $dependency['version'] . ' for ' . $installedModule['name'] . '. Either the version in dependency is incorrect or you need to re-sync from repository. We also did not find any API client service that can do this. Please add API Client service for the repository and re-sync. If sync does not solve the problem, please contact module developer.';
                    }

                    $this->addToQueueTasksAndResults($task, $moduleType, $installedModule, null, 'fail', $analyseLogs);
                } else if (Version::equal($requestedModule['version'], $installedModule['update_version'])) {
                    if ($installedModule['installed'] != '1') {
                        $this->addToQueueTasksAndResults('install', $moduleType, $installedModule);
                    } else {
                        if ($task !== 'first') {
                            $task = 'update';
                        }
                        $this->addToQueueTasksAndResults($task, $moduleType, $installedModule);
                    }

                    // $this->addToQueueTasksAndResults($task, $moduleType, $installedModule);

                    if ($moduleType === 'views' &&
                        array_key_exists('is_subview', $installedModule) &&
                        $installedModule['is_subview'] == 0
                    ) {
                        $installedModule['id'] = $installedModule['id'] . '-public';
                        $installedModule['name'] = ($installedModule['display_name'] ?? $installedModule['name']) . ' (Public)';
                        $installedModule['repo'] = $installedModule['repo'] . '-public';

                        if ($installedModule['installed'] != '1') {
                            $this->addToQueueTasksAndResults('install', $moduleType, $installedModule);
                        } else {
                            if ($task !== 'first') {
                                $task = 'update';
                            }
                            $this->addToQueueTasksAndResults($task, $moduleType, $installedModule);
                        }
                        // $this->addToQueueTasksAndResults('install', $moduleType, $installedModule);
                    }
                }
            } else {
                $apiClientService = $this->getApiClientServices($requestedModule);
                if ($apiClientService) {
                    $analyseLogs = 'Dependencies require version ' . $requestedModule['version'] . ' for ' . $installedModule['name'] . '. Either the version in dependency is incorrect or you need to sync ' . $apiClientService['name'] . ' repository to get the latest version. If sync does not solve the problem, please contact module developer.';
                } else {
                    $analyseLogs = 'Dependencies require version ' . $requestedModule['version'] . ' for ' . $installedModule['name'] . '. Either the version in dependency is incorrect or you need to re-sync from repository. We also did not find any API client service that can do this. Please add API Client service for the repository and re-sync. If sync does not solve the problem, please contact module developer.';
                }

                $this->addToQueueTasksAndResults($task, $moduleType, $installedModule, null, 'fail', $analyseLogs);
            }
        } else {
            if ($installedModule['installed'] != '1') {
                $this->addToQueueTasksAndResults('install', $moduleType, $installedModule);

                if ($moduleType === 'views' &&
                    array_key_exists('is_subview', $installedModule) &&
                    $installedModule['is_subview'] == 0
                ) {
                    $installedModule['id'] = $installedModule['id'] . '-public';
                    $installedModule['name'] = ($installedModule['display_name'] ?? $installedModule['name']) . ' (Public)';
                    $installedModule['repo'] = $installedModule['repo'] . '-public';

                    $this->addToQueueTasksAndResults('install', $moduleType, $installedModule);
                }
            }
        }
    }

    protected function checkComposerAndAddToQueue($composerPackages)
    {
        if (!isset($composerPackages['composer']['require']) && !isset($composerPackages['require'])) {
            return true;
        }

        $composerRequire = [];

        if (isset($composerPackages['composer']['require']) && count($composerPackages['composer']['require']) > 0) {
            $composerRequire = $composerPackages['composer']['require'];
        } else if (isset($composerPackages['require']) && count($composerPackages['require']) === 0) {
            $composerRequire = $composerPackages['require'];
        }

        if (count($composerRequire) === 0) {
            return true;
        }

        try {
            $composerJsonFile = $this->helper->decode(file_get_contents(base_path('external/composer.json')), true);
        } catch (\throwable $exception) {
            $composerJsonFile = false;
        }

        foreach ($composerRequire as $composerPackage => $version) {
            $installExternal = false;
            $hasConfigChange = false;
            $hasPatch = false;

            if ($composerJsonFile && isset($composerJsonFile['require'])) {
                if (!isset($composerJsonFile['require'][$composerPackage])) {
                    $composerJsonFile['require'][$composerPackage] = $version;
                    $installExternal = true;
                }

                if (isset($composerPackages['config'])) {
                    if (isset($composerPackages['config']['allow-plugins'][$composerPackage])) {
                        $composerJsonFile['config']['allow-plugins'][$composerPackage] = $composerPackages['config']['allow-plugins'][$composerPackage];
                        $hasConfigChange = true;
                    }
                    $allowPlugins = $composerPackages['config']['allow-plugins'];
                    unset($composerPackages['config']['allow-plugins']);
                    $composerJsonFile['config'] = array_merge_recursive_distinct($composerJsonFile['config'], $composerPackages['config']);
                    $composerPackages['config']['allow-plugins'] = $allowPlugins;
                }

                if (isset($composerPackages['extra'])) {
                    if (isset($composerPackages['extra']['patches'][$composerPackage])) {
                        $composerJsonFile['extra']['patches'][$composerPackage][$this->helper->firstKey($composerPackages['extra']['patches'][$composerPackage])] = base_path($this->helper->first($composerPackages['extra']['patches'][$composerPackage]));
                        $hasPatch = true;
                    }
                    $patches = $composerPackages['extra']['patches'];
                    unset($composerPackages['extra']['patches']);
                    $composerJsonFile['extra'] = array_merge_recursive_distinct($composerJsonFile['extra'], $composerPackages['extra']);
                    $composerPackages['extra']['patches'] = $patches;
                }

                if ($installExternal || $hasConfigChange || $hasPatch) {
                    $package['id'] = explode('/', $composerPackage)[1];
                    $package['name'] = $composerPackage;
                    $package['repo'] = 'Via composer';
                    $package['composerJsonFile'] = $composerJsonFile;
                    if ($hasConfigChange) {
                        $package['hasConfigChange'] = true;
                    }
                    if ($hasPatch) {
                        $package['hasPatch'] = true;
                    }

                    $this->addToQueueTasksAndResults('first', 'external', $package, $version);
                }
            } else {
                $package['id'] = '0';
                $package['name'] = $composerPackage;
                $package['repo'] = 'Via composer';
                $analyseLogs = 'Error reading composer json file from the external directory.';
                $this->addToQueueTasksAndResults('first', 'external', $package, $version, 'fail', $analyseLogs);
            }
        }
    }

    protected function addToQueueTasksAndResults($taskName, $moduleType, $module, $version = null, $analyseResult = 'pass', $analyseResultLogs = '-',)
    {
        if (isset($module['id'])) {
            $moduleId = $module['id'];
        }

        if ($analyseResult === 'fail') {
            $moduleId = '0';
            $module['id'] = $module['name'];
        }

        if (!isset($this->queueTasks[$taskName][$moduleType][$module['id']])) {
            $this->queueTasks[$taskName][$moduleType][$module['id']] = [];
            $this->queueTasks[$taskName][$moduleType][$module['id']]['id'] = $moduleId;
            $this->queueTasks[$taskName][$moduleType][$module['id']]['name'] = $module['name'];
            $this->queueTasks[$taskName][$moduleType][$module['id']]['module_type'] = $moduleType;
            if (!$version) {
                $this->queueTasks[$taskName][$moduleType][$module['id']]['version'] =
                    ($taskName === 'update' && $module['update_version'] && $module['update_version'] !== '') ? $module['version'] . ' -> ' . $module['update_version'] : $module['version'];
            } else {
                $this->queueTasks[$taskName][$moduleType][$module['id']]['version'] = $version;
            }
            $this->queueTasks[$taskName][$moduleType][$module['id']]['repo'] = $module['repo'];
            if (isset($module['composerJsonFile'])) {
                $this->queueTasks[$taskName][$moduleType][$module['id']]['composerJsonFile'] = $module['composerJsonFile'];
                if (isset($module['hasConfigChange'])) {
                    $this->queueTasks[$taskName][$moduleType][$module['id']]['hasConfigChange'] = true;
                }
                if (isset($module['hasPatch'])) {
                    $this->queueTasks[$taskName][$moduleType][$module['id']]['hasPatch'] = true;
                }
            }
        }

        if (!isset($this->results[$taskName][$moduleType][$module['id']])) {
            $this->results[$taskName][$moduleType][$module['id']]['analyse'] = $analyseResult;
            $this->results[$taskName][$moduleType][$module['id']]['analyse_logs'] = $analyseResultLogs;
            $this->results[$taskName][$moduleType][$module['id']]['precheck'] = '-';
            $this->results[$taskName][$moduleType][$module['id']]['precheck_logs'] = '-';
            $this->results[$taskName][$moduleType][$module['id']]['result'] = '-';
            $this->results[$taskName][$moduleType][$module['id']]['result_logs'] = '-';
        }
    }

    // protected function getComposerJsonFile()
    // {
    //     if (file_exists(base_path('external/composer.lock'))) {
    //         unlink(base_path('external/composer.lock'));
    //     }

    //     try {
    //         return $this->helper->decode($this->localContent->read('external/composer.json'), true);
    //     } catch (\throwable $exception) {
    //         return false;
    //     }
    // }
}