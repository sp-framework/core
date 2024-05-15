<?php

namespace System\Base\Installer\Packages\Setup\Schema\Modules;

use Phalcon\Db\Column;

class Queues
{
    public function columns()
    {
        return
            [
               'columns' => [
                    new Column(
                        'id',
                        [
                            'type'          => Column::TYPE_MEDIUMINTEGER,
                            'notNull'       => true,
                            'autoIncrement' => true,
                            'primary'       => true,
                        ]
                    ),
                    new Column(
                        'processed',
                        [
                            'type'    => Column::TYPE_BOOLEAN,
                            'notNull' => true,
                        ]
                    ),
                    new Column(
                        'processed_at',
                        [
                            'type'    => Column::TYPE_VARCHAR,
                            'size'    => 100,
                            'notNull' => false,
                        ]
                    ),
                    new Column(
                        'analysed',
                        [
                            'type'    => Column::TYPE_BOOLEAN,
                            'notNull' => false,
                        ]
                    ),
                    new Column(
                        'analysed_at',
                        [
                            'type'    => Column::TYPE_VARCHAR,
                            'size'    => 100,
                            'notNull' => false,
                        ]
                    ),
                    new Column(
                        'analysed_result',
                        [
                            'type'    => Column::TYPE_TEXT,
                            'notNull' => false,
                        ]
                    ),
                    new Column(
                        'tasks',
                        [
                            'type'    => Column::TYPE_JSON,
                            'notNull' => false,
                        ]
                    ),
                    new Column(
                        'tasks_result',
                        [
                            'type'    => Column::TYPE_TEXT,
                            'notNull' => false,
                        ]
                    ),
                    new Column(
                        'tasks_count',
                        [
                            'type'    => Column::TYPE_SMALLINTEGER,
                            'notNull' => false,
                        ]
                    ),
                ]
            ];
    }
}