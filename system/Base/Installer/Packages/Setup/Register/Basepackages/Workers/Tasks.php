<?php

namespace System\Base\Installer\Packages\Setup\Register\Basepackages\Workers;

class Tasks
{
    protected $db;

    protected $ff;

    protected $databasetype;

    public function register($db, $ff, $databasetype)
    {
        $this->db = $db;

        $this->ff = $ff;

        $this->databasetype = $databasetype;

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
        if ($this->databasetype !== 'db') {
            $callStore = $this->ff->store('basepackages_workers_calls');

            $dbCall = $callStore->findBy(['name', '=', 'ProcessEmailQueue']);
        } else {
            $dbCall =
                $this->db->fetchAll(
                    "SELECT * FROM basepackages_workers_calls WHERE name = :name",
                    Enum::FETCH_ASSOC,
                    [
                        "name" => 'ProcessEmailQueue',
                    ]
                );
        }

        if ($dbCall && count($dbCall) > 0) {
            $dbCall = $dbCall[0];
        }

        $taskArr = [];

        //Email High Priority (encrypted - with codes)
        $taskEntry =
            [
                'name'              => 'Email (Confidential)',
                'description'       => 'High priority emails that are confidential (with passwords or codes) that are time sensitive.',
                'exec_type'         => 'php',
                'cid'               => $dbCall['id'],
                'call_args'         => '{"priority":"1", "confidential":true}',
                'schedule_id'       => 1,
                'is_on_demand'      => 0,
                'priority'          => 10,
                'enabled'           => 1,
                'type'              => 0,
                'status'            => 1
            ];
        array_push($taskArr, $taskEntry);

        //Email High Priority
        $taskEntry =
            [
                'name'              => 'Email (High Priority)',
                'description'       => 'High priority emails.',
                'exec_type'         => 'php',
                'cid'               => $dbCall['id'],
                'call_args'         => '{"priority":"1"}',
                'schedule_id'       => 0,
                'is_on_demand'      => 1,
                'priority'          => 10,
                'enabled'           => 0,
                'type'              => 0,
                'status'            => 0
            ];
        array_push($taskArr, $taskEntry);

        //Email Medium Priority
        $taskEntry =
            [
                'name'              => 'Email (Medium Priority)',
                'description'       => 'Medium priority emails like notification emails.',
                'exec_type'         => 'php',
                'cid'               => $dbCall['id'],
                'call_args'         => '{"priority":"2"}',
                'schedule_id'       => 0,
                'is_on_demand'      => 1,
                'priority'          => 10,
                'enabled'           => 0,
                'type'              => 0,
                'status'            => 0
            ];
        array_push($taskArr, $taskEntry);

        //Email Low Priority
        $taskEntry =
            [
                'name'              => 'Email (Low Priority)',
                'description'       => 'Low priority emails like notification emails.',
                'exec_type'         => 'php',
                'cid'               => $dbCall['id'],
                'call_args'         => '{"priority":"3"}',
                'schedule_id'       => 0,
                'is_on_demand'      => 1,
                'priority'          => 10,
                'enabled'           => 0,
                'type'              => 0,
                'status'            => 0
            ];
        array_push($taskArr, $taskEntry);

        if ($this->databasetype !== 'db') {
            $dbCall = $callStore->findBy(['name', '=', 'ProcessImportExportQueue']);
        } else {
            $dbCall =
                $this->db->fetchAll(
                    "SELECT * FROM basepackages_workers_calls WHERE name = :name",
                    Enum::FETCH_ASSOC,
                    [
                        "name" => 'ProcessImportExportQueue',
                    ]
                );
        }

        if ($dbCall && count($dbCall) > 0) {
            $dbCall = $dbCall[0];
        }

        //Import/Export (Export)
        $taskEntry =
            [
                'name'              => 'Import/Export (Export)',
                'description'       => 'Import/Export Tools - Export',
                'exec_type'         => 'php',
                'cid'               => $dbCall['id'],
                'call_args'         => '{"process":"export"}',
                'schedule_id'       => 0,
                'is_on_demand'      => 1,
                'priority'          => 10,
                'enabled'           => 0,
                'type'              => 0,
                'status'            => 0
            ];
        array_push($taskArr, $taskEntry);

        //Import/Export (Import)
        $taskEntry =
            [
                'name'              => 'Import/Export (Low Priority)',
                'description'       => 'Import/Export Tools - Import',
                'exec_type'         => 'php',
                'cid'               => $dbCall['id'],
                'call_args'         => '{"process":"import"}',
                'schedule_id'       => 0,
                'is_on_demand'      => 1,
                'priority'          => 5,
                'enabled'           => 0,
                'type'              => 0,
                'status'            => 0
            ];
        array_push($taskArr, $taskEntry);

        if ($this->databasetype !== 'db') {
            $dbCall = $callStore->findBy(['name', '=', 'ProcessDbSync']);
        } else {
            $dbCall =
                $this->db->fetchAll(
                    "SELECT * FROM basepackages_workers_calls WHERE name = :name",
                    Enum::FETCH_ASSOC,
                    [
                        "name" => 'ProcessDbSync',
                    ]
                );
        }

        if ($dbCall && count($dbCall) > 0) {
            $dbCall = $dbCall[0];
        }


        //DB Sync (Hybrid Mode)
        $taskEntry =
            [
                'name'              => 'DB Sync (Hybric Mode)',
                'description'       => 'Update database with changed made to the FF Store.',
                'exec_type'         => 'php',
                'cid'               => $dbCall['id'],
                'call_args'         => '{}',
                'schedule_id'       => 0,
                'is_on_demand'      => 1,
                'priority'          => 10,
                'enabled'           => 0,
                'type'              => 0,
                'status'            => 0
            ];
        array_push($taskArr, $taskEntry);

        return $taskArr;
    }
}