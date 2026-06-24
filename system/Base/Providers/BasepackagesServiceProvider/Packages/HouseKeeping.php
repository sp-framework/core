<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages;

use League\Flysystem\UnableToCheckExistence;
use League\Flysystem\UnableToDeleteFile;
use System\Base\BasePackage;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Users\Accounts\BasepackagesUsersAccountsSessions;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Storages\Local;

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

        $this->addResponse('Ok', 0, ['taskResponse' => $taskResponse]);
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
                    $storage = $this->jsonData($storage, true);

                    $localStorage = (new Local)->initLocal($storage);

                    if ($this->config->databasetype === 'db') {
                        $files = $localStorage->getByParams(['conditions' => ['storages_id' => $storage['id']]]);
                    } else {
                        $files = $localStorage->getByParams(['conditions' => ['storages_id', '=', $storage['id']]]);
                    }

                    if ($files) {
                        $totalEntries = $totalEntries + count($files);

                        foreach ($files as $file) {
                            if ($file['orphan']) {//If file is orphan, we remove
                                $localStorage->removeFile($file['uuid'], true);

                                $clearedEntries['storage_' . $storage['id']]['file_' . $file['uuid']] = ['package_class' => $file['package_class'], 'package_row_id' => $file['package_row_id'], 'reason' => 'file orphan'];
                            } else if ($file['is_pointer']) {
                                try {
                                    if (!$this->localContent->fileExists($file['uuid_location'] . $file['org_file_name'])) {
                                        $localStorage->removeFile($file['uuid'], true, true, false);

                                        $clearedEntries['storage_' . $storage['id']]['file_' . $file['uuid']] = ['package_class' => $file['package_class'], 'package_row_id' => $file['package_row_id'], 'reason' => $file['uuid_location'] . $file['org_file_name'] . ' pointer file does not exists!'];

                                        continue;
                                    }
                                } catch (\throwable | UnableToCheckExistence $e) {
                                    $localStorage->removeFile($file['uuid'], true, true, false);

                                    $clearedEntries['storage_' . $storage['id']]['file_' . $file['uuid']] = ['package_class' => $file['package_class'], 'package_row_id' => $file['package_row_id'], 'reason' => $e->getMessage()];
                                }
                            } else {//else we check if the package row id exists and remove if package row id does not exists.
                                try {
                                    $packageClass = str_replace('_', '\\', $file['package_class']);

                                    $packageClass = new $packageClass;

                                    $packageRow = $packageClass->getById($file['package_row_id']);

                                    if ($packageRow) {
                                        //Find UUID in package row information
                                        $key = recursive_array_search($file['uuid'], $packageRow);

                                        if (!$key) {
                                            $localStorage->removeFile($file['uuid'], true);

                                            $clearedEntries['storage_' . $storage['id']]['file_' . $file['uuid']] = ['package_class' => $file['package_class'], 'package_row_id' => $file['package_row_id'], 'reason' => 'package_row_id does not have file assigned, file should be orphan!'];
                                        }
                                    } else {
                                        $localStorage->removeFile($file['uuid'], true);

                                        $clearedEntries['storage_' . $storage['id']]['file_' . $file['uuid']] = ['package_class' => $file['package_class'], 'package_row_id' => $file['package_row_id'], 'reason' => 'package_row_id does not exist!'];
                                    }
                                } catch (\throwable $e) {
                                    $localStorage->removeFile($file['uuid'], true);

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

    protected function cleanUnusedTags()
    {
        // For tags, we cross check the package_row_ids with the package class. If it does not exists, we remove the tags.
        $tags = $this->basepackages->tags->getAll(true)->tags;

        $totalEntries = 0;
        $clearedEntries = [];

        if ($tags && count($tags) > 0) {
            $totalEntries = count($tags);

            foreach ($tags as $tagKey => $tag) {
                if (count($tag['package_row_ids']) === 0) {
                    $this->basepackages->tags->remove($tag['id']);

                    $clearedEntries[$tagKey] = ['package_class' => $tag['package_class'], 'package_row_ids' => $tag['package_row_ids'], 'reason' => 'Tag not being used!'];

                    continue;
                }

                try {
                    $packageClass = str_replace('_', '\\', $tag['package_class']);

                    $packageClass = new $packageClass;

                    foreach ($tag['package_row_ids'] as $packageRowKey => $packageRowId) {
                        $packageRow = $packageClass->getById($packageRowId);

                        if (!$packageRow) {
                            unset($tag['package_row_ids'][$packageRowKey]);

                            $clearedEntries[$tagKey] = ['package_class' => $tag['package_class'], 'package_row_id' => $packageRowId, 'reason' => 'package_row_id does not exist!'];
                        }
                    }

                    if (count($tag['package_row_ids']) > 0) {
                        $this->basepackages->tags->update($tag);
                    } else {
                        $this->basepackages->tags->remove($tag['id']);

                        $clearedEntries[$tagKey] = ['package_class' => $tag['package_class'], 'package_row_ids' => $tag['package_row_ids'], 'reason' => 'Tag not being used!'];
                    }
                } catch (\throwable $e) {
                    $this->basepackages->tags->remove($tag['id']);

                    $clearedEntries[$tagKey] = ['package_class' => $tag['package_class'], 'package_row_ids' => $tag['package_row_ids'], 'reason' => $e->getMessage()];
                }
            }
        }

        return ['totalEntries' => $totalEntries, 'clearedEntries' => $clearedEntries];
    }

    protected function cleanStaleSessions()
    {
        //Check session entries in DB and session entries in the var/cache/session directory. Clean old stale sessions.
        $sessionModel = new BasepackagesUsersAccountsSessions;

        if ($this->config->databasetype === 'db') {
            $sessions = $sessionModel::findAll();
        } else {
            $sessionStore = $this->ff->store($sessionModel->getSource());

            $sessions = $sessionStore->findAll();
        }

        $totalEntries = 0;
        $clearedEntries = [];

        if ($sessions && count($sessions) > 0) {
            $totalEntries = count($sessions);

            $now = (\Carbon\Carbon::now())->timestamp;

            foreach ($sessions as $session) {
                $remove = false;

                if ($now > $session['session_absolute_timeout']) {
                    $clearedEntries[$session['session_id']] = ['reason' => 'Absolute timeout!'];

                    $remove = true;
                } else if ($now > $session['session_idle_timeout']) {
                    $clearedEntries[$session['session_id']] = ['reason' => 'Idle timeout!'];

                    $remove = true;
                }

                if ($remove) {
                    try {
                        if ($this->localContent->fileExists('var/storage/cache/session/' . $session['session_id'])) {
                            $this->localContent->delete('var/storage/cache/session/' . $session['session_id']);
                        }

                        if ($this->config->databasetype === 'db') {
                            $sessionModel::findFirst(['id = ' . $session['id']]);

                            $sessionModel->delete();
                        } else {
                            $sessionStore->deleteById((int) $session['id']);
                        }
                    } catch (\throwable | UnableToCheckExistence | UnableToDeleteFile $e) {
                        $clearedEntries[$session['session_id']] = ['reason' => 'Error: ' . $e->getMessage()];
                    }
                }
            }
        }

        return ['totalEntries' => $totalEntries, 'clearedEntries' => $clearedEntries];
    }
}