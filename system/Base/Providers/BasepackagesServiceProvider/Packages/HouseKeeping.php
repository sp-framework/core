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

        if ($storages && count($storages) > 0) {
            foreach ($storages as $storage) {
                if ($storage['type'] === 'local') {
                    if ($this->config->databasetype === 'db') {
                        $files = $this->basepackages->storages->getFiles(['params' => ['conditions' => ['storages_id' => $storage['id']]]]);
                    } else {
                        $files = $this->basepackages->storages->getFiles(['params' => ['conditions' => ['storages_id', '=', $storage['id']]]]);
                    }

                    if ($files) {
                        foreach ($files as $file) {
                            if ($file['orphan']) {//If file is orphan, we remove
                                $this->basepackages->storages->removeFile($file['uuid'], $storage['permission'], true);
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
                                        }
                                    } else {
                                        $this->basepackages->storages->removeFile($file['uuid'], $storage['permission'], true);
                                    }
                                } catch (\throwable $e) {
                                    $this->basepackages->storages->removeFile($file['uuid'], $storage['permission'], true);
                                }
                            }
                        }
                    }
                }
            }

            return true;
        }

        return 'No Storage Configured!';
    }
}