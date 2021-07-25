<?php

namespace System\Base\Installer\Packages\Setup\Register\Basepackages\Workers;

use Phalcon\Helper\Json;

class Workers
{
    public function register($db)
    {
        $workersArr = $this->workers();

        foreach ($workersArr as $key => $worker) {
            $db->insertAsDict(
                'basepackages_workers_workers',
                $worker
            );
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

        for ($x = 3; $x <= 10; $x++) {
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