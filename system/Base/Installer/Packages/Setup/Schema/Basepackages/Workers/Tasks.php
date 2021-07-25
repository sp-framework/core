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
                            'type'    => Column::TYPE_VARCHAR,
                            'size'    => 2048,
                            'notNull' => false,
                        ]
                    ),
                    new Column(
                        'function',
                        [
                            'type'          => Column::TYPE_VARCHAR,
                            'size'          => 1027,
                            'notNull'       => true,
                        ]
                    ),
                    new Column(
                        'schedule_id',
                        [
                            'type'          => Column::TYPE_SMALLINTEGER,
                            'notNull'       => true,
                        ]
                    ),
                    new Column(
                        'is_external',
                        [
                            'type'          => Column::TYPE_TINYINTEGER,
                            'notNull'       => false,
                        ]
                    ),
                    new Column(
                        'is_raw',
                        [
                            'type'          => Column::TYPE_TINYINTEGER,
                            'notNull'       => false,
                        ]
                    ),
                    new Column(
                        'priority',
                        [
                            'type'          => Column::TYPE_TINYINTEGER,
                            'notNull'       => true,
                        ]
                    ),
                    new Column(
                        'enabled',
                        [
                            'type'          => Column::TYPE_TINYINTEGER,
                            'notNull'       => true,
                        ]
                    ),
                    new Column(
                        'status',
                        [
                            'type'          => Column::TYPE_TINYINTEGER,
                            'notNull'       => false,
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
                        'email',
                        [
                            'type'          => Column::TYPE_VARCHAR,
                            'size'          => 1024,
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