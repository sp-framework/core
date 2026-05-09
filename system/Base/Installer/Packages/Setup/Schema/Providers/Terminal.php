<?php

namespace System\Base\Installer\Packages\Setup\Schema\Providers;

use Phalcon\Db\Column;

class Terminal
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
                    'hostname',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 50,
                        'notNull' => true,
                    ]
                ),
                new Column(
                    'banner',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 1024,
                        'notNull' => true,
                    ]
                )
            ]
        ];
    }
}