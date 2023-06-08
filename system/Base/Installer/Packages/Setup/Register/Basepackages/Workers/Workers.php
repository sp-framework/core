<?php

namespace System\Base\Installer\Packages\Setup\Register\Basepackages\Workers;

use Phalcon\Helper\Json;

class Workers
{
    public function register($db, $ff)
    {
        $workersArr = $this->workers();

        foreach ($workersArr as $key => $worker) {
            if ($db) {
                $db->insertAsDict('basepackages_workers_workers', $worker);
            }

            if ($ff) {
                $workerStore = $ff->store('basepackages_workers_workers');

                $workerStore->updateOrInsert($worker);
            }
        }
    }

    protected function workers()
    {
        $workersArr = [];

        for ($x = 1; $x <= 2; $x++) {
            array_push($workersArr,
                [
                    'name'          => 'Worker ' . $x,
                    'status'        => 0,
                    'enabled'       => 1
                ]
            );
        }

        for ($x = 3; $x <= 100; $x++) {
            array_push($workersArr,
                [
                    'name'          => 'Worker ' . $x,
                    'status'        => 0,
                    'enabled'       => 0
                ]
            );
        }

        return $workersArr;
    }
}