<?php

namespace System\Base\Installer\Packages\Setup\Schema;

use Phalcon\Db\Column;

class EmailServices
{
    public function columns()
    {
        return
            [
               'columns' => [
                    new Column(
                        'id',
                        [
                            'type'          => Column::TYPE_TINYINTEGER,
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
                            'notNull' => false
                        ]
                    ),
                    new Column(
                        'host',
                        [
                            'type'    => Column::TYPE_VARCHAR,
                            'size'    => 100,
                            'notNull' => true,
                        ]
                    ),
                    new Column(
                        'port',
                        [
                            'type'    => Column::TYPE_MEDIUMINTEGER,
                            'notNull' => true,
                        ]
                    ),
                    new Column(
                        'auth',
                        [
                            'type'    => Column::TYPE_TINYINTEGER,
                            'notNull' => false,
                        ]
                    ),
                    new Column(
                        'username',
                        [
                            'type'    => Column::TYPE_VARCHAR,
                            'size'    => 100,
                            'notNull' => false,
                        ]
                    ),
                    new Column(
                        'password',
                        [
                            'type'    => Column::TYPE_VARCHAR,
                            'size'    => 50,
                            'notNull' => false,
                        ]
                    ),
                    new Column(
                        'from_address',
                        [
                            'type'    => Column::TYPE_VARCHAR,
                            'size'    => 100,
                            'notNull' => false,
                        ]
                    ),
                    new Column(
                        'encryption',
                        [
                            'type'    => Column::TYPE_TINYINTEGER,
                            'notNull' => false,
                        ]
                    ),
                    new Column(
                        'allow_html_body',
                        [
                            'type'    => Column::TYPE_TINYINTEGER,
                            'notNull' => false,
                        ]
                    )
                ]
            ];
    }
}