<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages;

use System\Base\BasePackage;

class HouseKeeping extends BasePackage
{
    public function init()
    {
        return $this;
    }

    public function run(array $tasks)
    {
        $taskResponse = [];

        foreach ($tasks as $task) {
            if (method_exists($this, $task)) {
                $taskResponse[$task] = $this->$task();
            }
        }

        trace([$taskResponse]);
    }

    protected function cleanStorageOrphans()
    {
        //We remove orphans that are marked by the developer
        //If in case a developer misses a reference and the file db entry gets stuck (not marked as orphan but the reference is deleted)
        //We scan for the referring entry and if it does not exists, we delete it.
        //In case there are multiple file entries pointing to the same referring entry, we cross check the UUID in the referring db entry.
        //If the entry does not match, we remove the file entry.
        $storages = $this->basepackages->storages->getAll()->storages;

        $totalEntries = 0;
        $clearedEntries = [];

        if ($storages && count($storages) > 0) {
            foreach ($storages as $storage) {
                $clearedEntries['storage_' . $storage['id']] = [];

                if ($storage['type'] === 'local') {
                    if ($this->config->databasetype === 'db') {
                        $files = $this->basepackages->storages->getFiles(['params' => ['conditions' => ['storages_id' => $storage['id']]]]);
                    } else {
                        $files = $this->basepackages->storages->getFiles(['params' => ['conditions' => ['storages_id', '=', $storage['id']]]]);
                    }

                    if ($files) {
                        $totalEntries = $totalEntries + count($files);

                        foreach ($files as $file) {
                            if ($file['orphan']) {//If file is orphan, we remove
                                $this->basepackages->storages->removeFile($file['uuid'], $storage['permission'], true);

                                $clearedEntries['storage_' . $storage['id']]['file_' . $file['uuid']] = ['package_class' => $file['package_class'], 'package_row_id' => $file['package_row_id'], 'reason' => 'file orphan'];
                            } else {//else we check if the package row id exists and remove if package row id does not exists.
                                try {
                                    $packageClass = str_replace('_', '\\', $file['package_class']);

                                    $packageClass = new $packageClass;

                                    $packageRow = $packageClass->getById($file['package_row_id']);

                                    if ($packageRow) {
                                        //Find UUID in package row information
                                        $key = recursive_array_search($file['uuid'], $packageRow);

                                        if (!$key) {
                                            $this->basepackages->storages->removeFile($file['uuid'], $storage['permission'], true);

                                            $clearedEntries['storage_' . $storage['id']]['file_' . $file['uuid']] = ['package_class' => $file['package_class'], 'package_row_id' => $file['package_row_id'], 'reason' => 'package_row_id does not have file assigned, file should be orphan!'];
                                        }
                                    } else {
                                        $this->basepackages->storages->removeFile($file['uuid'], $storage['permission'], true);

                                        $clearedEntries['storage_' . $storage['id']]['file_' . $file['uuid']] = ['package_class' => $file['package_class'], 'package_row_id' => $file['package_row_id'], 'reason' => 'package_row_id does not exist!'];
                                    }
                                } catch (\throwable $e) {
                                    $this->basepackages->storages->removeFile($file['uuid'], $storage['permission'], true);

                                     $clearedEntries['storage_' . $storage['id']]['file_' . $file['uuid']] = ['package_class' => $file['package_class'], 'package_row_id' => $file['package_row_id'], 'reason' => $e->getMessage()];
                                }
                            }
                        }
                    }
                }
            }
        }

        return ['totalEntries' => $totalEntries, 'clearedEntries' => $clearedEntries];
    }

    protected function cleanActivityLogs()
    {
        // For activity logs, we cross check the package_row_id with the package class. If it does not exists, we remove the activity logs.
        $logs = $this->basepackages->activityLogs->getAll(true)->activityLogs;

        $totalEntries = 0;
        $clearedEntries = [];

        if ($logs && count($logs) > 0) {
            $totalEntries = count($logs);

            foreach ($logs as $logKey => $log) {
                try {
                    $packageClass = str_replace('_', '\\', $log['package_class']);

                    $packageClass = new $packageClass;

                    $packageRow = $packageClass->getById($log['package_row_id']);

                    if (!$packageRow) {
                        $this->basepackages->activityLogs->remove($log['id']);

                        $clearedEntries[$logKey] = ['package_class' => $log['package_class'], 'package_row_id' => $log['package_row_id'], 'reason' => 'package_row_id does not exist!'];
                    }
                } catch (\throwable $e) {
                    $this->basepackages->activityLogs->remove($log['id']);

                    $clearedEntries[$logKey] = ['package_class' => $log['package_class'], 'package_row_id' => $log['package_row_id'], 'reason' => $e->getMessage()];
                }
            }
        }

        return ['totalEntries' => $totalEntries, 'clearedEntries' => $clearedEntries];
    }
}