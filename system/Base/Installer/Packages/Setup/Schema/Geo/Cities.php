<?php

namespace System\Base\Installer\Packages\Setup\Schema\Geo;

use Phalcon\Db\Column;
use Phalcon\Db\Index;

class Cities
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
                        'size'    => 100,
                        'notNull' => true,
                    ]
                ),
                new Column(
                    'latitude',
                    [
                        'type'    => Column::TYPE_DECIMAL,
                        'size'    => 10,
                        'scale'   => 8,
                        'notNull' => false,
                    ]
                ),
                new Column(
                    'longitude',
                    [
                        'type'    => Column::TYPE_DECIMAL,
                        'size'    => 11,
                        'scale'   => 8,
                        'notNull' => false,
                    ]
                ),
                new Column(
                    'state_id',
                    [
                        'type'    => Column::TYPE_INTEGER,
                        'notNull' => true,
                    ]
                ),
                new Column(
                    'country_id',
                    [
                        'type'    => Column::TYPE_INTEGER,
                        'notNull' => true,
                    ]
                ),
                new Column(
                    'user_added',
                    [
                        'type'    => Column::TYPE_TINYINTEGER,
                        'notNull' => false,
                    ]
                )
            ],
            'options' => [
                'TABLE_COLLATION' => 'utf8mb4_unicode_ci'
            ]
        ];
    }
}