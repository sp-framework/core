<?php

namespace System\Base\Installer\Packages\Setup\Register\Basepackages\Workers;

class Tasks
{
    public function register($db, $ff)
    {
        $taskArr = $this->systemSchedules();

        foreach ($taskArr as $key => $task) {
            if ($db) {
                $db->insertAsDict('basepackages_workers_tasks', $task);
            }

            if ($ff) {
                $taskStore = $ff->store('basepackages_workers_tasks');

                $taskStore->updateOrInsert($task);
            }
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
                'exec_type'         => 'call',
                'call'              => 'processemailqueue',
                'call_args'         => '{"priority":"1"}',
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
                'exec_type'         => 'call',
                'call'              => 'processemailqueue',
                'call_args'         => '{"priority":"2"}',
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
                'exec_type'         => 'call',
                'call'              => 'processemailqueue',
                'call_args'         => '{"priority":"3"}',
                'schedule_id'       => 0,
                'is_on_demand'      => 1,
                'priority'          => 10,
                'enabled'           => 0,
                'type'              => 0
            ];
        array_push($taskArr, $taskEntry);

        //Import/Export (Export)
        $taskEntry =
            [
                'name'              => 'Import/Export (Export)',
                'description'       => 'Import/Export Tools - Export',
                'exec_type'         => 'call',
                'call'              => 'processimportexportqueue',
                'call_args'         => '{"process":"export"}',
                'schedule_id'       => 0,
                'is_on_demand'      => 1,
                'priority'          => 10,
                'enabled'           => 0,
                'type'              => 0
            ];
        array_push($taskArr, $taskEntry);

        //Import/Export (Import)
        $taskEntry =
            [
                'name'              => 'Import/Export (Low Priority)',
                'description'       => 'Import/Export Tools - Import',
                'exec_type'         => 'call',
                'call'              => 'processimportexportqueue',
                'call_args'         => '{"process":"import"}',
                'schedule_id'       => 0,
                'is_on_demand'      => 1,
                'priority'          => 5,
                'enabled'           => 0,
                'type'              => 0
            ];
        array_push($taskArr, $taskEntry);

        //DB Sync (Hybrid Mode)
        $taskEntry =
            [
                'name'              => 'DB Sync (Hybric Mode)',
                'description'       => 'Update database with changed made to the FF Store.',
                'exec_type'         => 'call',
                'call'              => 'processdbsync',
                'call_args'         => '{}',
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