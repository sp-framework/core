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
            )
        ];

        if ($oldQueue && isset($oldQueue['sync'])) {
            $queue['sync'] = $oldQueue['sync'];
        }

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

    protected function getTasksCount(&$queue, $analyse = false)
    {
        $queue['total'] = 0;
        $queue['tasks_count'] = [];
        $tasks = $queue['tasks'];
        if ($analyse) {
            $tasks = $queue['results'];
        }
        foreach ($tasks as $taskType => $task) {
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
                                            if (!isset($this->queueTasks[$taskName][$bundleType])) {
                                                $this->queueTasks[$taskName][$bundleType] = [];
                                            }

                                            $bundleModule = $this->apps->types->getAppTypeByRepo($bundles['repo']);

                                            if ($bundleModule) {
                                                $this->addToQueueTasksAndResults($taskName, $bundleType, $bundleModule);
                                            } else {
                                                $this->addToQueueTasksAndResults($taskName, $bundleType, $bundles, null, 'fail', $this->getApiClientServices($bundles, true));
                                            }
                                        } else if ($bundleType === 'core') {
                                            $bundleModule = $this->modules->packages->getPackageByRepo($bundles['repo']);

                                            if ($bundles['version'] !== $bundleModule['version']) {
                                                $this->addToQueueTasksAndResults('update', 'packages', $bundleModule);
                                            }
                                        } else {
                                            if ($bundleType === 'external') {
                                                continue;
                                            }

                                            foreach ($bundles as $bundleKey => $bundle) {
                                                $bundleModuleMethod = 'get' . ucfirst(substr($bundleType, 0, -1)) . 'ByRepo';
                                                $bundleModuleType = $bundleType;

                                                $bundleModule = $this->modules->{$bundleModuleType}->$bundleModuleMethod($bundle['repo']);

                                                if ($bundleModule) {
                                                    $this->addToQueueTasksAndResults($taskName, $bundleType, $bundleModule);

                                                    if ($bundleType === 'views' &&
                                                        array_key_exists('is_subview', $bundleModule) &&
                                                        $bundleModule['is_subview'] == 0
                                                    ) {
                                                        $bundleModule['id'] = $bundleModule['id'] . '-public';
                                                        $bundleModule['name'] = ($bundleModule['display_name'] ?? $bundleModule['name']) . ' (Public)';
                                                        $bundleModule['repo'] = $bundleModule['repo'] . '-public';

                                                        $this->addToQueueTasksAndResults('install', $bundleType, $bundleModule);
                                                    }
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
                                            $this->addToQueueTasksAndResults('update', 'packages', $core);
                                        } else {
                                            $updateToVersion = $core['version'] . ' -> ' . $core['update_version'];

                                            if (Version::greaterThan($dependencies['version'], $core['update_version'])) {
                                                $updateToVersion = $core['version'] . ' -> ' . $dependencies['version'];
                                            }

                                            $analyseLogs =
                                                'Dependencies require version ' . $dependencies['version'] . ' for core. Either the version in dependency is incorrect or you need to sync core repository to get the latest version. If sync does not solve the problem, please contact module developer.';

                                            $this->addToQueueTasksAndResults('update', 'packages', $core, $updateToVersion, 'fail',$analyseLogs);
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

                                                    $this->addToQueueTasksAndResults($taskName, $dependencyType, $appType, null, 'fail', $analyseLogs);
                                                } else if ($appType['update_version'] === $dependencies['version']) {
                                                    if ($appType['installed'] != '1') {
                                                        $this->addToQueueTasksAndResults('install', $dependencyType, $appType);
                                                    } else {
                                                        $this->addToQueueTasksAndResults('update', $dependencyType, $appType);
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

                                                $this->addToQueueTasksAndResults($taskName, $dependencyType, $appType, null, 'fail', $analyseLogs);
                                            }
                                        } else {
                                            if ($appType['installed'] != '1') {
                                                $this->addToQueueTasksAndResults('install', $dependencyType, $appType);
                                            }
                                        }
                                    } else {
                                        if ($appType['installed'] != '1') {
                                            $this->addToQueueTasksAndResults('install', $dependencyType, $appType);
                                        }
                                    }
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

                                                            $this->addToQueueTasksAndResults($taskName, $dependencyType, $dependencyModule, null, 'fail', $analyseLogs);
                                                        } else if ($dependencyModule['update_version'] === $dependency['version']) {
                                                            if ($dependencyModule['installed'] != '1') {
                                                                $this->addToQueueTasksAndResults('install', $dependencyType, $dependencyModule);

                                                                if ($dependencyType === 'views' &&
                                                                    array_key_exists('is_subview', $dependencyModule) &&
                                                                    $dependencyModule['is_subview'] == 0
                                                                ) {
                                                                    $dependencyModule['id'] = $dependencyModule['id'] . '-public';
                                                                    $dependencyModule['name'] = ($dependencyModule['display_name'] ?? $dependencyModule['name']) . ' (Public)';
                                                                    $dependencyModule['repo'] = $dependencyModule['repo'] . '-public';

                                                                    $this->addToQueueTasksAndResults('install', $dependencyType, $dependencyModule);
                                                                }
                                                            } else {
                                                                $this->addToQueueTasksAndResults('update', $dependencyType, $dependencyModule);

                                                                if ($dependencyType === 'views' &&
                                                                    array_key_exists('is_subview', $dependencyModule) &&
                                                                    $dependencyModule['is_subview'] == 0
                                                                ) {
                                                                    $dependencyModule['id'] = $dependencyModule['id'] . '-public';
                                                                    $dependencyModule['name'] = ($dependencyModule['display_name'] ?? $dependencyModule['name']) . ' (Public)';
                                                                    $dependencyModule['repo'] = $dependencyModule['repo'] . '-public';

                                                                    $this->addToQueueTasksAndResults('update', $dependencyType, $dependencyModule);
                                                                }
                                                            }
                                                        }
                                                    } else {
                                                        $analyseLogs = 'Dependencies require version ' . $dependency['version'] . ' for ' . $dependencyModule['name'] . '. Either the version in dependency is incorrect or you need to sync ' . $apiClientService['name'] . ' repository to get the latest version. If sync does not solve the problem, please contact module developer.';

                                                        $apiClientService = $this->getApiClientServices($dependency);
                                                        if (!$apiClientService) {
                                                            $analyseLogs = 'Dependencies require version ' . $dependency['version'] . ' for ' . $dependencyModule['name'] . '. Either the version in dependency is incorrect or you need to re-sync from repository. We also did not find any API client service that can do this. Please add API Client service for the repository and re-sync. If sync does not solve the problem, please contact module developer.';
                                                        }

                                                        $this->addToQueueTasksAndResults($taskName, $dependencyType, $dependencyModule, null, 'fail', $analyseLogs);
                                                    }
                                                } else {
                                                    if ($dependencyModule['installed'] != '1') {
                                                        $this->addToQueueTasksAndResults('install', $dependencyType, $dependencyModule);

                                                        if ($dependencyType === 'views' &&
                                                            array_key_exists('is_subview', $dependencyModule) &&
                                                            $dependencyModule['is_subview'] == 0
                                                        ) {
                                                            $dependencyModule['id'] = $dependencyModule['id'] . '-public';
                                                            $dependencyModule['name'] = ($dependencyModule['display_name'] ?? $dependencyModule['name']) . ' (Public)';
                                                            $dependencyModule['repo'] = $dependencyModule['repo'] . '-public';

                                                            $this->addToQueueTasksAndResults('install', $dependencyType, $dependencyModule);
                                                        }
                                                    }
                                                }
                                            } else {
                                                if ($dependencyModule['installed'] != '1') {
                                                    $this->addToQueueTasksAndResults('install', $dependencyType, $dependencyModule);

                                                    if ($dependencyType === 'views' &&
                                                        array_key_exists('is_subview', $dependencyModule) &&
                                                        $dependencyModule['is_subview'] == 0
                                                    ) {
                                                        $dependencyModule['id'] = $dependencyModule['id'] . '-public';
                                                        $dependencyModule['name'] = ($dependencyModule['display_name'] ?? $dependencyModule['name']) . ' (Public)';
                                                        $dependencyModule['repo'] = $dependencyModule['repo'] . '-public';

                                                        $this->addToQueueTasksAndResults('install', $dependencyType, $dependencyModule);
                                                    }
                                                }
                                            }
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
        $this->getTasksCount($queue, true);

        if ($this->update($queue)) {
            $this->addResponse('Analysed Queue', 0, ['queueTasks' => $this->queueTasks, 'queueTasksCounter' => $queue['tasks_count']]);

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

    protected function addToQueueTasksAndResults($taskName, $moduleType, $module, $version = null, $analyseResult = 'pass', $analyseResultLogs = '-',)
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
                $this->queueTasks[$taskName][$moduleType][$module['id']]['version'] =
                    ($taskName === 'update' && $module['update_version'] && $module['update_version'] !== '') ? $module['version'] . ' -> ' . $module['update_version'] : $module['version'];
            } else {
                $this->queueTasks[$taskName][$moduleType][$module['id']]['version'] = $version;
            }
            $this->queueTasks[$taskName][$moduleType][$module['id']]['repo'] = $module['repo'];
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