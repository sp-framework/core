<?php

declare(strict_types=1);

/**
 * SP Framework
 *
 * @package     System\Base\Installer\Packages\Setup\Register\Basepackages\Workers
 * @copyright   Copyright (c) 2026
 * @link        https://github.com/sp-framework/core
 */

namespace System\Base\Installer\Packages\Setup\Register\Basepackages\Workers;

use Phalcon\Db\Enum;

/**
 * Seeds recurring background tasks into basepackages_workers_tasks.
 */
class Tasks
{
    /**
     * PDO database connection adapter.
     *
     * @var mixed
     */
    protected mixed $db = null;

    /**
     * FlatFile database manager.
     *
     * @var mixed
     */
    protected mixed $ff = null;

    /**
     * Database driver type.
     *
     * @var string
     */
    protected string $databasetype = 'hybrid';

    /**
     * Registers default background worker tasks.
     *
     * @param mixed  $db           PDO database connection adapter.
     * @param mixed  $ff           FlatFile database manager.
     * @param string $databasetype Database driver type (db, ff, hybrid).
     *
     * @return void
     */
    public function register(mixed $db, mixed $ff, string $databasetype): void
    {
        $this->db = $db;
        $this->ff = $ff;
        $this->databasetype = $databasetype;

        $taskArr = $this->systemSchedules();

        foreach ($taskArr as $task) {
            if ($db) {
                $db->insertAsDict('basepackages_workers_tasks', $task);
            }

            if ($ff) {
                $taskStore = $ff->store('basepackages_workers_tasks');

                $taskStore->updateOrInsert($task);
            }
        }
    }

    /**
     * Compiles default email queue and housekeeping task definitions.
     *
     * @return array<int, array<string, mixed>>
     */
    protected function systemSchedules(): array
    {
        $taskArr = [];

        //Email High Priority (encrypted - with codes)
        $taskArr[] = [
            'name'              => 'Email (Confidential)',
            'description'       => 'High priority emails that are confidential (with passwords or codes) that are time sensitive.',
            'exec_type'         => 'php',
            'cid'               => $this->getCallId('ProcessEmailQueue'),
            'call_args'         => '{"priority":"1", "confidential":true}',
            'schedule_id'       => 1,
            'is_on_demand'      => 0,
            'priority'          => 10,
            'enabled'           => 1,
            'type'              => 0,
            'job_log_mode'      => 3,
            'status'            => 1
        ];

        // Email High Priority
        $taskArr[] = [
            'name'              => 'Email (High Priority)',
            'description'       => 'High priority emails.',
            'exec_type'         => 'php',
            'cid'               => $this->getCallId('ProcessEmailQueue'),
            'call_args'         => '{"priority":"1"}',
            'schedule_id'       => 0,
            'is_on_demand'      => 1,
            'priority'          => 10,
            'enabled'           => 0,
            'type'              => 0,
            'job_log_mode'      => 3,
            'status'            => 0
        ];

        // Email Medium Priority
        $taskArr[] = [
            'name'              => 'Email (Medium Priority)',
            'description'       => 'Medium priority emails like notification emails.',
            'exec_type'         => 'php',
            'cid'               => $this->getCallId('ProcessEmailQueue'),
            'call_args'         => '{"priority":"2"}',
            'schedule_id'       => 0,
            'is_on_demand'      => 1,
            'priority'          => 10,
            'enabled'           => 0,
            'type'              => 0,
            'job_log_mode'      => 3,
            'status'            => 0
        ];

        // Email Low Priority
        $taskArr[] = [
            'name'              => 'Email (Low Priority)',
            'description'       => 'Low priority emails like notification emails.',
            'exec_type'         => 'php',
            'cid'               => $this->getCallId('ProcessEmailQueue'),
            'call_args'         => '{"priority":"3"}',
            'schedule_id'       => 0,
            'is_on_demand'      => 1,
            'priority'          => 10,
            'enabled'           => 0,
            'type'              => 0,
            'job_log_mode'      => 3,
            'status'            => 0
        ];

        //Import/Export (Export)
        $taskArr[] = [
            'name'              => 'Import/Export (Export)',
            'description'       => 'Import/Export Tools - Export',
            'exec_type'         => 'php',
            'cid'               => $this->getCallId('ProcessImportExportQueue'),
            'call_args'         => '{"process":"export"}',
            'schedule_id'       => 0,
            'is_on_demand'      => 1,
            'priority'          => 10,
            'enabled'           => 0,
            'type'              => 0,
            'job_log_mode'      => 3,
            'status'            => 0
        ];

        //Import/Export (Export)
        $taskArr[] = [
            'name'              => 'Import/Export (Low Priority)',
            'description'       => 'Import/Export Tools - Import',
            'exec_type'         => 'php',
            'cid'               => $this->getCallId('ProcessImportExportQueue'),
            'call_args'         => '{"process":"import"}',
            'schedule_id'       => 0,
            'is_on_demand'      => 1,
            'priority'          => 5,
            'enabled'           => 0,
            'type'              => 0,
            'job_log_mode'      => 3,
            'status'            => 0
        ];

        //DB Sync (Hybrid Mode)
        $taskArr[] = [
            'name'              => 'DB Sync (Hybric Mode)',
            'description'       => 'Update database with changed made to the FF Store.',
            'exec_type'         => 'php',
            'cid'               => $this->getCallId('ProcessDbSync'),
            'call_args'         => '{}',
            'schedule_id'       => 0,
            'is_on_demand'      => 1,
            'priority'          => 10,
            'enabled'           => 0,
            'type'              => 0,
            'job_log_mode'      => 3,
            'status'            => 0
        ];

        //System Backup
        $taskArr[] = [
            'name'              => 'System Backup',
            'description'       => 'Run system backup once a day.',
            'exec_type'         => 'call',
            'cid'               => $this->getCallId('ProcessSystemBackup'),
            'call_args'         => '{"apps_dir":"false","systems_dir":"false","public_dir":"false","private_dir":"false","html_compiled_dir":"false","var_dir":"false","external_dir":"false","external_vendor_dir":"false","old_backups_dir":"false","database":"false","keys":"false","password_protect":"","notes":"","rclone_to_gdrive":"false","rclone_remote_drive":"","rclone_remote_path":""}',
            'schedule_id'       => 7,//Everyday at midnight
            'is_on_demand'      => 0,
            'priority'          => 10,//Run before Housekeeping
            'enabled'           => 1,
            'type'              => 0,
            'job_log_mode'      => 4,//Monthly logs
            'status'            => 0
        ];

        //Housekeeping
        $taskArr[] = [
            'name'              => 'HouseKeeping',
            'description'       => 'Run house keeping jobs on various packages like cleaning orphan files, stale sessions, etc.',
            'exec_type'         => 'call',
            'cid'               => $this->getCallId('ProcessHousekeeping'),
            'call_args'         => '{"tasks":["cleanStorageOrphans","cleanActivityLogs","cleanUnusedTags","cleanStaleSessions"]}',
            'schedule_id'       => 7,//Everyday at midnight
            'is_on_demand'      => 0,
            'priority'          => 8,//Run after backup has been complete
            'enabled'           => 1,
            'type'              => 0,
            'job_log_mode'      => 4,//Monthly logs
            'status'            => 0
        ];

        //Check for Core updates
        $taskArr[] = [
            'name'              => 'Check for update (Core)',
            'description'       => 'Run monthly checks for core updates.',
            'exec_type'         => 'call',
            'cid'               => $this->getCallId('ProcessRepoSync'),
            'call_args'         => '{"api_id":1}',
            'schedule_id'       => 11,//Everyday at midnight
            'is_on_demand'      => 0,
            'priority'          => 10,//Run after backup has been complete
            'enabled'           => 1,
            'type'              => 0,
            'job_log_mode'      => 5,//Yearly logs
            'status'            => 0//Keeping disabled as API needs to be configured before enabling it.
        ];

        return $taskArr;
    }

    /**
     * Get Call ID
     *
     * @param string    $callName   Call name to lookup.
     *
     * @return int
     */
    protected function getCallId($callName): int
    {
        $dbCall = null;

        if ($this->databasetype !== 'db' && $this->ff) {
            $callStore = $this->ff->store('basepackages_workers_calls');

            $dbCall = $callStore?->findBy(['name', '=', $callName]);
        } elseif ($this->db) {
            $dbCall = $this->db->fetchAll(
                'SELECT * FROM basepackages_workers_calls WHERE name = :name',
                Enum::FETCH_ASSOC,
                ['name' => $callName]
            );
        }

        return (is_array($dbCall) && isset($dbCall[0]['id'])) ? (int) $dbCall[0]['id'] : 1;
    }
}