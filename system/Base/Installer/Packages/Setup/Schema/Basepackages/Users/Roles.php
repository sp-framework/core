<?php

namespace System\Base\Installer\Packages\Setup\Schema\Basepackages\Users;

use Phalcon\Db\Column;
use Phalcon\Db\Index;

class Roles
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
                        'name',
                        [
                            'type'    => Column::TYPE_VARCHAR,
                            'size'    => 50,
                            'notNull' => true,
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
                        'type',//0 for system and 1 for user.
                        [
                            'type'    => Column::TYPE_BOOLEAN,
                            'notNull' => true,
                        ]
                    ),
                    new Column(
                        'permissions',
                        [
                            'type'    => Column::TYPE_TEXT,
                            'notNull' => true,
                        ]
                    )
                ],
                'indexes' => [
                    new Index(
                        'column_UNIQUE',
                        [
                            'name',
                        ],
                        'UNIQUE'
                    )
                ],
                'options' => [
                    'TABLE_COLLATION' => 'utf8mb4_general_ci'
                ]
            ];
    }
}