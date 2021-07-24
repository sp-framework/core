<?php

namespace System\Base\Providers\BasepackagesServiceProvider\Packages;

use GO\Scheduler;
use Phalcon\Helper\Json;
use System\Base\BasePackage;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Workers\Schedules;
use System\Base\Providers\BasepackagesServiceProvider\Packages\Workers\Tasks;

class Workers extends BasePackage
{
    public $workers;

    protected $schedulerSettings = [];

    public function init(bool $resetCache = false)
    {
        $this->schedules = (new Schedules())->init();

        $this->tasks = (new Tasks())->init();

        if ($this->checkTempPath()) {
            $this->schedulerSettings['tempDir'] = base_path('var/workers/');
        }

        $this->scheduler = new Scheduler($this->schedulerSettings);

        return $this;
    }

    public function run()
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

    protected function checkTempPath()
    {
        if (!is_dir(base_path('var/workers/'))) {
            if (!mkdir(base_path('var/workers/'), 0777, true)) {
                return false;
            }
        }

        return true;
    }
}

