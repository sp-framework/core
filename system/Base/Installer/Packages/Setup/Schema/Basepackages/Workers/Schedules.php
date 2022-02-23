<?php

namespace System\Base\Installer\Packages\Setup\Schema\Basepackages\Workers;

use Phalcon\Db\Column;

class Schedules
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
                        'schedule',
                        [
                            'type'          => Column::TYPE_VARCHAR,
                            'size'          => 1027,
                            'notNull'       => true,
                        ]
                    ),
                    new Column(
                        'type',
                        [
                            'type'          => Column::TYPE_TINYINTEGER,
                            'notNull'       => true,
                        ]
                    )
                ]
            ];
    }
}