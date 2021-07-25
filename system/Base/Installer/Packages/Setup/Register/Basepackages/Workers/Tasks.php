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
                'function'          => 'emailqueuehighpriority',
                'schedule_id'       => 1,
                'priority'          => 10,
                'enabled'           => 1,
                'type'              => 0,
            ];
        array_push($taskArr, $taskEntry);

        //Email Medium Priority
        $taskEntry =
            [
                'name'              => 'Email (Medium Priority)',
                'description'       => 'Medium priority emails like notification emails.',
                'function'          => 'emailqueuemediumpriority',
                'schedule_id'       => 2,
                'priority'          => 5,
                'enabled'           => 1,
                'type'              => 0,
            ];
        array_push($taskArr, $taskEntry);

        //Email Low Priority
        $taskEntry =
            [
                'name'              => 'Email (Low Priority)',
                'description'       => 'Low priority emails.',
                'function'          => 'emailqueuelowpriority',
                'schedule_id'       => 4,
                'priority'          => 1,
                'enabled'           => 1,
                'type'              => 0,
            ];
        array_push($taskArr, $taskEntry);

        return $taskArr;
    }
}