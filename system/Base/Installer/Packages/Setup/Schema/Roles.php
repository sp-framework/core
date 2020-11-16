<?php

namespace System\Base\Installer\Packages\Setup\Schema;

use Phalcon\Db\Column;

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
                        'parent_id',
                        [
                            'type'    => Column::TYPE_INTEGER,
                            'notNull' => true,
                        ]
                    ),
                    new Column(
                        'permissions',
                        [
                            'type'    => Column::TYPE_MEDIUMTEXT,
                            'notNull' => true,
                        ]
                    ),
                ]
            ];
    }
}