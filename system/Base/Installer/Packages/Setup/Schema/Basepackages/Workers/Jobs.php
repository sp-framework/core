<?php

namespace System\Base\Installer\Packages\Setup\Schema\Basepackages\Workers;

use Phalcon\Db\Column;
use Phalcon\Db\Index;

class Jobs
{
    public function columns()
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
                    )
                ]
            ];
    }

    public function indexes()
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