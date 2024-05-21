<?php

namespace System\Base\Providers\ModulesServiceProvider;

use System\Base\BasePackage;
use System\Base\Providers\ModulesServiceProvider\Model\ServiceProviderModulesQueues;
use z4kn4fein\SemVer\Version;

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
                                $results[$taskName][$moduleType][$module['id']]['analyse_logs'] = '-';
                                $results[$taskName][$moduleType][$module['id']]['precheck'] = '-';
                                $results[$taskName][$moduleType][$module['id']]['precheck_logs'] = '-';
                                $results[$taskName][$moduleType][$module['id']]['result'] = '-';
                                $results[$taskName][$moduleType][$module['id']]['result_logs'] = '-';
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
                                                $results[$taskName][$bundleType][$bundleModule['id']]['analyse_logs'] = '-';
                                                $results[$taskName][$bundleType][$bundleModule['id']]['precheck'] = '-';
                                                $results[$taskName][$bundleType][$bundleModule['id']]['precheck_logs'] = '-';
                                                $results[$taskName][$bundleType][$bundleModule['id']]['result'] = '-';
                                                $results[$taskName][$bundleType][$bundleModule['id']]['result_logs'] = '-';
                                            } else {
                                                $queueTasks[$taskName][$bundleType][0]['id'] = '0';
                                                $queueTasks[$taskName][$bundleType][0]['name'] = $bundles['name'];
                                                $queueTasks[$taskName][$bundleType][0]['module_type'] = 'apptype';
                                                $queueTasks[$taskName][$bundleType][0]['version'] = $bundles['version'];
                                                $queueTasks[$taskName][$bundleType][0]['repo'] = $bundles['repo'];
                                                $results[$taskName][$bundleType][$bundles['name']]['analyse'] = 'fail';
                                                $results[$taskName][$bundleType][$bundles['name']]['analyse_logs'] = $this->getApiClientServices($bundles, true);
                                                $results[$taskName][$bundleType][$bundles['name']]['precheck'] = '-';
                                                $results[$taskName][$bundleType][$bundles['name']]['precheck_logs'] = '-';
                                                $results[$taskName][$bundleType][$bundles['name']]['result'] = '-';
                                                $results[$taskName][$bundleType][$bundles['name']]['result_logs'] = '-';
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
                                                    $results['update']['packages'][$bundleModule['id']]['analyse_logs'] = '-';
                                                    $results['update']['packages'][$bundleModule['id']]['precheck'] = '-';
                                                    $results['update']['packages'][$bundleModule['id']]['precheck_logs'] = '-';
                                                    $results['update']['packages'][$bundleModule['id']]['result'] = '-';
                                                    $results['update']['packages'][$bundleModule['id']]['result_logs'] = '-';
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
                                                        $results[$taskName][$bundleType][$bundleModule['id']]['analyse_logs'] = '-';
                                                        $results[$taskName][$bundleType][$bundleModule['id']]['precheck'] = '-';
                                                        $results[$taskName][$bundleType][$bundleModule['id']]['precheck_logs'] = '-';
                                                        $results[$taskName][$bundleType][$bundleModule['id']]['result'] = '-';
                                                        $results[$taskName][$bundleType][$bundleModule['id']]['result_logs'] = '-';
                                                    }
                                                } else {
                                                    $queueTasks[$taskName][$bundleType][$bundleKey]['id'] = '0';
                                                    $queueTasks[$taskName][$bundleType][$bundleKey]['name'] = $bundle['name'];
                                                    $queueTasks[$taskName][$bundleType][$bundleKey]['module_type'] = $bundleType;
                                                    $queueTasks[$taskName][$bundleType][$bundleKey]['version'] = $bundle['version'];
                                                    $queueTasks[$taskName][$bundleType][$bundleKey]['repo'] = $bundle['repo'];
                                                    $results[$taskName][$bundleType][$bundle['name']]['analyse'] = 'fail';
                                                    $results[$taskName][$bundleType][$bundle['name']]['analyse_logs'] = $this->getApiClientServices($bundle, true);
                                                    $results[$taskName][$bundleType][$bundle['name']]['precheck'] = '-';
                                                    $results[$taskName][$bundleType][$bundle['name']]['precheck_logs'] = '-';
                                                    $results[$taskName][$bundleType][$bundle['name']]['result'] = '-';
                                                    $results[$taskName][$bundleType][$bundle['name']]['result_logs'] = '-';
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
                            if ($dependencyType === 'core') {
                                $core = $this->modules->packages->getPackageByRepo($dependencies['repo']);

                                if ($dependencies['version'] !== $core['version']) {
                                    if (Version::greaterThan($dependencies['version'], $core['version'])) {
                                        if (isset($core['update_version']) &&
                                            $core['update_version'] !== '' &&
                                            $core['update_version'] === $dependencies['version']
                                        ) {
                                            if (!isset($queueTasks['update']['packages'][$core['id']]['id'])) {
                                                $queueTasks['update']['packages'][$core['id']]['id'] = $core['id'];
                                                $queueTasks['update']['packages'][$core['id']]['name'] = $core['display_name'] ?? $core['name'];
                                                $queueTasks['update']['packages'][$core['id']]['module_type'] = $core['module_type'];
                                                $queueTasks['update']['packages'][$core['id']]['version'] = $core['version'] . ' -> ' . $core['update_version'];
                                                $queueTasks['update']['packages'][$core['id']]['repo'] = $core['repo'];
                                                $results['update']['packages'][$core['id']]['analyse'] = 'pass';
                                                $results['update']['packages'][$core['id']]['analyse_logs'] = '-';
                                                $results['update']['packages'][$core['id']]['precheck'] = '-';
                                                $results['update']['packages'][$core['id']]['precheck_logs'] = '-';
                                                $results['update']['packages'][$core['id']]['result'] = '-';
                                                $results['update']['packages'][$core['id']]['result_logs'] = '-';
                                            }
                                        } else {
                                            $queueTasks['update']['packages'][$core['id']]['id'] = $core['id'];
                                            $queueTasks['update']['packages'][$core['id']]['name'] = $core['name'];
                                            $queueTasks['update']['packages'][$core['id']]['module_type'] = $core['module_type'];

                                            if (Version::greaterThan($dependencies['version'], $core['update_version'])) {
                                                $queueTasks['update']['packages'][$core['id']]['version'] = $core['version'] . ' -> ' . $dependencies['version'];
                                            } else {
                                                $queueTasks['update']['packages'][$core['id']]['version'] = $core['version'] . ' -> ' . $core['update_version'];
                                            }
                                            $queueTasks['update']['packages'][$core['id']]['repo'] = $core['repo'];
                                            $results['update']['packages'][$core['name']]['analyse'] = 'fail';
                                            $results['update']['packages'][$core['name']]['analyse_logs'] = 'Dependencies require version ' . $dependencies['version'] . ' for core. Either the version in dependency is incorrect or you need to sync core repository to get the latest version. If sync does not solve the problem, please contact module developer.';
                                            $results['update']['packages'][$core['name']]['precheck'] = '-';
                                            $results['update']['packages'][$core['name']]['precheck_logs'] = '-';
                                            $results['update']['packages'][$core['name']]['result'] = '-';
                                            $results['update']['packages'][$core['name']]['result_logs'] = '-';
                                        }
                                    }
                                }
                            } else if ($dependencyType === 'apptype') {
                                if (!isset($queueTasks[$taskName][$dependencyType])) {
                                    $queueTasks[$taskName][$dependencyType] = [];
                                }

                                $appType = $this->apps->types->getAppTypeByRepo($dependencies['repo']);

                                if ($appType) {
                                    if ($dependencies['version'] !== $appType['version']) {
                                        if (Version::greaterThan($dependencies['version'], $appType['version'])) {
                                            if (isset($appType['update_version']) &&
                                                $appType['update_version'] !== ''
                                            ) {
                                                if (Version::greaterThan($dependencies['version'], $appType['update_version'])) {
                                                    $queueTasks[$taskName][$dependencyType][$appType['id']]['id'] = '0';
                                                    $queueTasks[$taskName][$dependencyType][$appType['id']]['name'] = $appType['name'];
                                                    $queueTasks[$taskName][$dependencyType][$appType['id']]['module_type'] = $dependencyType;
                                                    $queueTasks[$taskName][$dependencyType][$appType['id']]['version'] = $appType['version'];
                                                    $queueTasks[$taskName][$dependencyType][$appType['id']]['repo'] = $appType['repo'];
                                                    $results[$taskName][$dependencyType][$appType['name']]['analyse'] = 'fail';
                                                    $apiClientService = $this->getApiClientServices($dependencies);

                                                    if (!$apiClientService) {
                                                        $results[$taskName][$dependencyType][$appType['name']]['analyse_logs'] = 'Dependencies require version ' . $dependencies['version'] . ' for ' . $appType['name'] . '. Either the version in dependency is incorrect or you need to re-sync from repository. We also did not find any API client service that can do this. Please add API Client service for the repository and re-sync. If sync does not solve the problem, please contact module developer.';
                                                    } else {
                                                        $results[$taskName][$dependencyType][$appType['name']]['analyse_logs'] = 'Dependencies require version ' . $dependencies['version'] . ' for ' . $appType['name'] . '. Either the version in dependency is incorrect or you need to sync ' . $apiClientService['name'] . ' repository to get the latest version. If sync does not solve the problem, please contact module developer.';
                                                    }
                                                    $results[$taskName][$dependencyType][$appType['name']]['precheck'] = '-';
                                                    $results[$taskName][$dependencyType][$appType['name']]['precheck_logs'] = '-';
                                                    $results[$taskName][$dependencyType][$appType['name']]['result'] = '-';
                                                    $results[$taskName][$dependencyType][$appType['name']]['result_logs'] = '-';
                                                } else if ($appType['update_version'] === $dependencies['version']) {
                                                    if ($appType['installed'] != '1') {
                                                        if (!isset($queueTasks['install'][$dependencyType][$appType['id']])) {
                                                            $queueTasks['install'][$dependencyType][$appType['id']] = [];
                                                            $queueTasks['install'][$dependencyType][$appType['id']]['id'] = $appType['id'];
                                                            $queueTasks['install'][$dependencyType][$appType['id']]['name'] = $appType['display_name'] ?? $appType['name'];
                                                            $queueTasks['install'][$dependencyType][$appType['id']]['module_type'] = $dependencyType;
                                                            $queueTasks['install'][$dependencyType][$appType['id']]['version'] = ($appType['update_version'] ?? $appType['version']);
                                                            $queueTasks['install'][$dependencyType][$appType['id']]['repo'] = $appType['repo'];
                                                            $results['install'][$dependencyType][$appType['id']]['analyse'] = 'pass';
                                                            $results['install'][$dependencyType][$appType['id']]['analyse_logs'] = '-';
                                                            $results['install'][$dependencyType][$appType['id']]['precheck'] = '-';
                                                            $results['install'][$dependencyType][$appType['id']]['precheck_logs'] = '-';
                                                            $results['install'][$dependencyType][$appType['id']]['result'] = '-';
                                                            $results['install'][$dependencyType][$appType['id']]['result_logs'] = '-';
                                                        }
                                                    }
                                                }
                                            } else {
                                                $queueTasks[$taskName][$dependencyType][$appType['id']]['id'] = '0';
                                                $queueTasks[$taskName][$dependencyType][$appType['id']]['name'] = $appType['name'];
                                                $queueTasks[$taskName][$dependencyType][$appType['id']]['module_type'] = $dependencyType;
                                                $queueTasks[$taskName][$dependencyType][$appType['id']]['version'] = $appType['version'];
                                                $queueTasks[$taskName][$dependencyType][$appType['id']]['repo'] = $appType['repo'];
                                                $results[$taskName][$dependencyType][$appType['name']]['analyse'] = 'fail';
                                                $apiClientService = $this->getApiClientServices($dependencies);

                                                if (!$apiClientService) {
                                                    $results[$taskName][$dependencyType][$appType['name']]['analyse_logs'] = 'Dependencies require version ' . $dependencies['version'] . ' for ' . $appType['name'] . '. Either the version in dependency is incorrect or you need to re-sync from repository. We also did not find any API client service that can do this. Please add API Client service for the repository and re-sync. If sync does not solve the problem, please contact module developer.';
                                                } else {
                                                    $results[$taskName][$dependencyType][$appType['name']]['analyse_logs'] = 'Dependencies require version ' . $dependencies['version'] . ' for ' . $appType['name'] . '. Either the version in dependency is incorrect or you need to sync ' . $apiClientService['name'] . ' repository to get the latest version. If sync does not solve the problem, please contact module developer.';
                                                }
                                                $results[$taskName][$dependencyType][$appType['name']]['precheck'] = '-';
                                                $results[$taskName][$dependencyType][$appType['name']]['precheck_logs'] = '-';
                                                $results[$taskName][$dependencyType][$appType['name']]['result'] = '-';
                                                $results[$taskName][$dependencyType][$appType['name']]['result_logs'] = '-';
                                            }
                                        }
                                    } else {
                                        if ($appType['installed'] != '1') {
                                            if (!isset($queueTasks['install'][$dependencyType][$appType['id']])) {
                                                $queueTasks['install'][$dependencyType][$appType['id']] = [];
                                                $queueTasks['install'][$dependencyType][$appType['id']]['id'] = $appType['id'];
                                                $queueTasks['install'][$dependencyType][$appType['id']]['name'] = $appType['display_name'] ?? $appType['name'];
                                                $queueTasks['install'][$dependencyType][$appType['id']]['module_type'] = $dependencyType;
                                                $queueTasks['install'][$dependencyType][$appType['id']]['version'] = ($appType['update_version'] ?? $appType['version']);
                                                $queueTasks['install'][$dependencyType][$appType['id']]['repo'] = $appType['repo'];
                                                $results['install'][$dependencyType][$appType['id']]['analyse'] = 'pass';
                                                $results['install'][$dependencyType][$appType['id']]['analyse_logs'] = '-';
                                                $results['install'][$dependencyType][$appType['id']]['precheck'] = '-';
                                                $results['install'][$dependencyType][$appType['id']]['precheck_logs'] = '-';
                                                $results['install'][$dependencyType][$appType['id']]['result'] = '-';
                                                $results['install'][$dependencyType][$appType['id']]['result_logs'] = '-';
                                            }
                                        }
                                    }
                                } else {
                                    $queueTasks[$taskName][$dependencyType][0]['id'] = '0';
                                    $queueTasks[$taskName][$dependencyType][0]['name'] = $dependencies['name'];
                                    $queueTasks[$taskName][$dependencyType][0]['module_type'] = $dependencyType;
                                    $queueTasks[$taskName][$dependencyType][0]['version'] = $dependencies['version'];
                                    $queueTasks[$taskName][$dependencyType][0]['repo'] = $dependencies['repo'];
                                    $results[$taskName][$dependencyType][$dependencies['name']]['analyse'] = 'fail';
                                    $results[$taskName][$dependencyType][$dependencies['name']]['analyse_logs'] = $this->getApiClientServices($dependencies, true);
                                    $results[$taskName][$dependencyType][$dependencies['name']]['precheck'] = '-';
                                    $results[$taskName][$dependencyType][$dependencies['name']]['precheck_logs'] = '-';
                                    $results[$taskName][$dependencyType][$dependencies['name']]['result'] = '-';
                                    $results[$taskName][$dependencyType][$dependencies['name']]['result_logs'] = '-';
                                }
                            } else {
                                if (count($dependencies) > 0) {
                                    trace([$dependencyType, $dependencies]);
                                }
                            }
                        }

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
                            $results[$taskName][$moduleType][$module['id']]['analyse_logs'] = '-';
                            $results[$taskName][$moduleType][$module['id']]['precheck'] = '-';
                            $results[$taskName][$moduleType][$module['id']]['precheck_logs'] = '-';
                            $results[$taskName][$moduleType][$module['id']]['result'] = '-';
                            $results[$taskName][$moduleType][$module['id']]['result_logs'] = '-';
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
}