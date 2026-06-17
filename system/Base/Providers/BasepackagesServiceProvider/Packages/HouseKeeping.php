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
        //Sync database entries with physical entries.
        //If entry is in db and file dont exist, remove from DB and vice versa.
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
                            if ($file['is_pointer']) {
                                continue;
                            }

                            if ($file['orphan']) {
                                $this->basepackages->storages->removeFile($file['uuid'], $storage['permission'], true);
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