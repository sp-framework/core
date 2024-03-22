<?php

namespace System\Base\Installer\Packages\Setup\Schema\Basepackages\Workers;

use Phalcon\Db\Column;

class Tasks
{
    public function columns()
    {
        return
            [
               'columns' => [
                    new Column(
                        'id',
                        [
                            'type'          => Column::TYPE_SMALLINTEGER,
                            'notNull'       => true,
                            'autoIncrement' => true,
                            'primary'       => true,
                        ]
                    ),
                    new Column(
                        'name',
                        [
                            'type'          => Column::TYPE_VARCHAR,
                            'size'          => 50,
                            'notNull'       => true,
                        ]
                    ),
                    new Column(
                        'description',
                        [
                            'type'          => Column::TYPE_VARCHAR,
                            'size'          => 2048,
                            'notNull'       => false,
                        ]
                    ),
                    new Column(
                        'schedule_id',
                        [
                            'type'          => Column::TYPE_SMALLINTEGER,
                            'notNull'       => false,
                        ]
                    ),
                    new Column(
                        'priority',
                        [
                            'type'          => Column::TYPE_TINYINTEGER,
                            'notNull'       => false,
                        ]
                    ),
                    new Column(
                        'is_on_demand',
                        [
                            'type'          => Column::TYPE_TINYINTEGER,
                            'notNull'       => false,
                        ]
                    ),
                    new Column(
                        'enabled',
                        [
                            'type'          => Column::TYPE_TINYINTEGER,
                            'notNull'       => true,
                        ]
                    ),
                    new Column(//Running, error
                        'status',
                        [
                            'type'          => Column::TYPE_TINYINTEGER,
                            'notNull'       => false,
                        ]
                    ),
                    new Column(//0 - system 1 - user
                        'type',
                        [
                            'type'          => Column::TYPE_TINYINTEGER,
                            'notNull'       => true,
                        ]
                    ),
                    new Column(//call, php, raw
                        'exec_type',
                        [
                            'type'          => Column::TYPE_VARCHAR,
                            'size'          => 50,
                            'notNull'       => true,
                        ]
                    ),
                    new Column(
                        'call',
                        [
                            'type'          => Column::TYPE_VARCHAR,
                            'size'          => 2048,
                            'notNull'       => false,
                        ]
                    ),
                    new Column(
                        'call_args',
                        [
                            'type'          => Column::TYPE_JSON,
                            'notNull'       => false,
                        ]
                    ),
                    new Column(
                        'php',
                        [
                            'type'          => Column::TYPE_TEXT,
                            'size'          => 2048,
                            'notNull'       => false,
                        ]
                        ),
                    new Column(
                        'php_args',
                        [
                            'type'          => Column::TYPE_JSON,
                            'notNull'       => false,
                        ]
                        ),
                    new Column(
                        'raw',
                        [
                            'type'          => Column::TYPE_TEXT,
                            'size'          => 2048,
                            'notNull'       => false,
                        ]
                    ),
                    new Column(
                        'raw_args',
                        [
                            'type'          => Column::TYPE_JSON,
                            'notNull'       => false,
                        ]
                    ),
                    new Column(
                        'pid',
                        [
                            'type'          => Column::TYPE_INTEGER,
                            'notNull'       => false,
                        ]
                    ),
                    new Column(
                        'previous_run',
                        [
                            'type'          => Column::TYPE_VARCHAR,
                            'size'          => 50,
                            'notNull'       => false,
                        ]
                    ),
                    new Column(
                        'next_run',
                        [
                            'type'          => Column::TYPE_VARCHAR,
                            'size'          => 50,
                            'notNull'       => false,
                        ]
                    ),
                    new Column(
                        'force_next_run',
                        [
                            'type'          => Column::TYPE_TINYINTEGER,
                            'notNull'       => false,
                        ]
                    ),
                    new Column(
                        'email',
                        [
                            'type'          => Column::TYPE_VARCHAR,
                            'size'          => 2048,
                            'notNull'       => false,
                        ]
                    ),
                    new Column(
                        'result',
                        [
                            'type'          => Column::TYPE_TEXT,
                            'notNull'       => false,
                        ]
                    ),
                ]
            ];
    }
}