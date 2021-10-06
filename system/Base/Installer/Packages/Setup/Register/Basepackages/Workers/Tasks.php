<?php

namespace System\Base\Installer\Packages\Setup\Register\Basepackages\Workers;

use Phalcon\Helper\Json;

class Tasks
{
    public function register($db)
    {
        $taskArr = $this->systemSchedules();

        foreach ($taskArr as $key => $task) {
            $db->insertAsDict(
                'basepackages_workers_tasks',
                $task
            );
        }
    }

    protected function systemSchedules()
    {
        $taskArr = [];

        //Email High Priority
        $taskEntry =
            [
                'name'              => 'Email (High Priority)',
                'description'       => 'High priority emails like password recovery emails.',
                'function'          => 'processemailqueue',
                'parameters'        => '{"priority":"1"}',
                'schedule_id'       => 0,
                'is_on_demand'      => 1,
                'priority'          => 10,
                'enabled'           => 0,
                'type'              => 0
            ];
        array_push($taskArr, $taskEntry);

        //Email Medium Priority
        $taskEntry =
            [
                'name'              => 'Email (Medium Priority)',
                'description'       => 'Medium priority emails like notification emails.',
                'function'          => 'processemailqueue',
                'parameters'        => '{"priority":"2"}',
                'schedule_id'       => 0,
                'is_on_demand'      => 1,
                'priority'          => 10,
                'enabled'           => 0,
                'type'              => 0
            ];
        array_push($taskArr, $taskEntry);

        //Email Low Priority
        $taskEntry =
            [
                'name'              => 'Email (Low Priority)',
                'description'       => 'Low priority emails like notification emails.',
                'function'          => 'processemailqueue',
                'parameters'        => '{"priority":"3"}',
                'schedule_id'       => 0,
                'is_on_demand'      => 1,
                'priority'          => 10,
                'enabled'           => 0,
                'type'              => 0
            ];
        array_push($taskArr, $taskEntry);

        return $taskArr;
    }
}