<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages\Workers;

use League\Flysystem\StorageAttributes;
use Phalcon\Helper\Json;
use System\Base\BasePackage;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Model\Workers\BasepackagesWorkersTasks;

class Tasks extends BasePackage
{
    protected $modelToUse = BasepackagesWorkersTasks::class;

    protected $packageName = 'tasks';

    protected $functionsDir = 'system/Base/Providers/BasepackagesServiceProvider/Packages/Workers/Functions/';

    public $tasks;

    public function init(bool $resetCache = false)
    {
        return $this;
    }

    public function getAllFunctions()
    {
        var_dump($this->functionsDir);
        $availableFunctionsArr =
            $this->localContent->listContents($this->functionsDir, true)
            ->filter(fn (StorageAttributes $attributes) => $attributes->isFile())
            ->map(fn (StorageAttributes $attributes) => $attributes->path())
            ->toArray();
        var_dump($availableFunctionsArr);

        $availableFunctions = [];

        if (count($availableFunctionsArr) > 0) {
            foreach ($availableFunctionsArr as $key => $function) {
                $function = ucfirst($function);
                $function = str_replace('/', '\\', $function);
                $function = str_replace('.php', '', $function);

                try {
                    $function = new $function();

                    $availableFunctions[$key]['func'] = $function->packageName;
                    $availableFunctions[$key]['name'] = $function->funcName;

                } catch (\Exception $e) {
                    throw $e;
                }
                var_dump($function);
            }
        }

        return $availableFunctions;
    }

    public function addTask(array $data)
    {
        //
    }

    public function updateTask(array $data)
    {
        //
    }

    public function removeTask(array $data)
    {
        //
    }

    public function runTask(int $id)
    {
        //This method will be called via CRON every minute.
        //Taskes that have next_run > current Unix Time will be executed as per their priority.
        //Priorities are set as numbers from 1-10 with 1 being the lowest priority and 10 being the highest.
        //Taskes can be registered to run at certain intervals.
        //Example: Run Email Queue with High priority - Priority set to 10 and process run every minute.
        // $logger = $this->logger;

        // var_dump($this->scheduler);

        // $this->scheduler->call(
        //     function () {
        //         echo 'Iran';
        //     }
        // )->inForeground();

        // $this->scheduler->work([0,10,20,30,40,50]);
    }
}

