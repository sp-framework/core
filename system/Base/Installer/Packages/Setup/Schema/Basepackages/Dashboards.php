<?php

namespace System\Base\Installer\Packages\Setup\Schema\Basepackages;

use Phalcon\Db\Column;

class Dashboards
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
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 50,
                        'notNull' => true,
                    ]
                ),
                new Column(
                    'app_id',
                    [
                        'type'    => Column::TYPE_INTEGER,
                        'notNull' => true
                    ]
                ),
                new Column(
                    'app_default',
                    [
                        'type'    => Column::TYPE_BOOLEAN,
                        'notNull' => true
                    ]
                ),
                new Column(
                    'created_by',
                    [
                        'type'    => Column::TYPE_INTEGER,
                        'notNull' => true
                    ]
                ),
                new Column(
                    'shared',
                    [
                        'type'    => Column::TYPE_JSON,
                        'notNull' => false
                    ]
                ),
                new Column(
                    'is_default',
                    [
                        'type'    => Column::TYPE_BOOLEAN,
                        'notNull' => false
                    ]
                ),
                new Column(
                    'settings',
                    [
                        'type'    => Column::TYPE_JSON,
                        'notNull' => true
                    ]
                )
            ]
        ];
    }
}