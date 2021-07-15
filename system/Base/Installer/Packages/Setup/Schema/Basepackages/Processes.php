<?php

namespace System\Base\Installer\Packages\Setup\Schema\Basepackages;

use Phalcon\Db\Column;

class Notifications
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
                        'root_process',
                        [
                            'type'          => Column::TYPE_INTEGER,
                            'notNull'       => false,
                        ]
                    ),
                    new Column(
                        'process_name',
                        [
                            'type'          => Column::TYPE_VARCHAR,
                            'size'          => 50,
                            'notNull'       => true,
                        ]
                    ),
                    new Column(
                        'status',
                        [
                            'type'    => Column::TYPE_TINYINTEGER,
                            'notNull' => true,
                        ]
                    ),
                    new Column(
                        'priority',
                        [
                            'type'    => Column::TYPE_TINYINTEGER,
                            'notNull' => true,
                        ]
                    ),
                ]
            ];
    }
}