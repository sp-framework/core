<?php

namespace System\Base\Installer\Packages\Setup\Schema\Basepackages;

use Phalcon\Db\Column;

class Widgets
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
                    'method',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 255,
                        'notNull' => true,
                    ]
                ),
                new Column(
                    'component_id',
                    [
                        'type'    => Column::TYPE_SMALLINTEGER,
                        'notNull' => true
                    ]
                ),
                new Column(
                    'multiple',
                    [
                        'type'    => Column::TYPE_BOOLEAN,
                        'notNull' => true
                    ]
                ),
                new Column(
                    'max_multiple',
                    [
                        'type'    => Column::TYPE_TINYINTEGER,
                        'notNull' => false
                    ]
                ),
                new Column(
                    'settings',
                    [
                        'type'    => Column::TYPE_JSON,
                        'notNull' => false
                    ]
                )
            ]
        ];
    }
}