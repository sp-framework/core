<?php

declare(strict_types=1);

/**
 * SP Framework
 *
 * @package     System\Base\Installer\Packages\Setup\Schema\Basepackages\Workers
 * @copyright   Copyright (c) 2026
 * @link        https://github.com/sp-framework/core
 */

namespace System\Base\Installer\Packages\Setup\Schema\Basepackages\Workers;

use Phalcon\Db\Column;
use Phalcon\Db\Index;

/**
 * Table schema definition for Jobs (Basepackages Workers Jobs).
 */
class Jobs
{
    /**
     * Defines table columns and data types.
     *
     * @return array<string, array<int, Column>> Columns definition array.
     */
    public function columns(): array
    {
        return
            [
               'columns' => [
                    new Column(
                        'id',
                        [
                            'type'          => Column::TYPE_INTEGER,
                            'notNull'       => true,
                            'autoIncrement' => true,
                            'primary'       => true,
                        ]
                    ),
                    new Column(
                        'task_id',
                        [
                            'type'          => Column::TYPE_SMALLINTEGER,
                            'notNull'       => true,
                        ]
                    ),
                    new Column(
                        'worker_id',
                        [
                            'type'          => Column::TYPE_TINYINTEGER,
                            'notNull'       => true,
                        ]
                    ),
                    new Column(//Call ID
                        'cid',
                        [
                            'type'          => Column::TYPE_INTEGER,
                            'notNull'       => true,
                        ]
                    ),
                    new Column(//Process Id
                        'pid',
                        [
                            'type'          => Column::TYPE_INTEGER,
                            'notNull'       => false,
                        ]
                    ),
                    new Column(//register call ids from basepackages_api_client_services_calls
                        'api_call_ids',
                        [
                            'type'          => Column::TYPE_JSON,
                            'notNull'       => false,
                        ]
                    ),
                    new Column(
                        'run_on',
                        [
                            'type'          => Column::TYPE_JSON,
                            'notNull'       => false,
                        ]
                    ),
                    new Column(
                        'status',
                        [
                            'type'          => Column::TYPE_TINYINTEGER,
                            'notNull'       => true,
                        ]
                    ),
                    new Column(//0 - system 1 - user
                        'type',
                        [
                            'type'          => Column::TYPE_TINYINTEGER,
                            'notNull'       => true,
                        ]
                    ),
                    new Column(
                        'execution_times',
                        [
                            'type'          => Column::TYPE_JSON,
                            'notNull'       => false,
                        ]
                    ),
                    new Column(
                        'total_execution_time',
                        [
                            'type'          => Column::TYPE_FLOAT,
                            'notNull'       => false,
                        ]
                    ),
                    new Column(//1 - per job run, 2 per hour, 3 per day
                        'job_log_mode',
                        [
                            'type'          => Column::TYPE_TINYINTEGER,
                            'notNull'       => true,
                            'default'       => 1
                        ]
                    ),
                    new Column(
                        'job_log_time',
                        [
                            'type'          => Column::TYPE_INTEGER,
                            'notNull'       => false
                        ]
                    ),
                    new Column(
                        'can_terminate',
                        [
                            'type'          => Column::TYPE_BOOLEAN,
                            'notNull'       => true
                        ]
                    ),
                    new Column(
                        'response_code',
                        [
                            'type'          => Column::TYPE_JSON,
                            'notNull'       => false,
                        ]
                    ),
                    new Column(
                        'response_message',
                        [
                            'type'          => Column::TYPE_JSON,
                            'notNull'       => false,
                        ]
                    ),
                    new Column(
                        'response_data',
                        [
                            'type'          => Column::TYPE_JSON,
                            'notNull'       => false,
                        ]
                    ),
                    new Column(
                        'email_results',
                        [
                            'type'          => Column::TYPE_JSON,
                            'notNull'       => false,
                        ]
                    )
                ]
            ];
    }

    /**
     * Defines table indexes and database constraints.
     *
     * @return array<int, Index> Indexes definition array.
     */
    public function indexes(): array
    {
        return
        [
            new Index(
                'column_INDEX',
                [
                    'task_id',
                    'job_log_mode',
                    'job_log_time'
                ],
                'INDEX'
            )
        ];
    }
}
