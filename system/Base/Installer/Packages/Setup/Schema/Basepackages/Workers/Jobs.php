<?php

namespace System\Base\Installer\Packages\Setup\Schema\Basepackages\Workers;

use Phalcon\Db\Column;

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
                    new Column(
                        'type',
                        [
                            'type'          => Column::TYPE_TINYINTEGER,
                            'notNull'       => true,
                        ]
                    ),
                    new Column(
                        'execution_time',
                        [
                            'type'          => Column::TYPE_FLOAT,
                            'notNull'       => false,
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
}