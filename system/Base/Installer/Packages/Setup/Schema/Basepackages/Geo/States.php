<?php

namespace System\Base\Installer\Packages\Setup\Schema\Basepackages\Geo;

use Phalcon\Db\Column;
use Phalcon\Db\Index;

class States
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
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 255,
                        'notNull'       => true,
                    ]
                ),
                new Column(
                    'state_code',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 10,
                        'notNull'       => false,
                    ]
                ),
                new Column(
                    'longitude',
                    [
                        'type'          => Column::TYPE_DECIMAL,
                        'size'          => 11,
                        'scale'         => 8,
                        'notNull'       => false,
                    ]
                ),
                new Column(
                    'latitude',
                    [
                        'type'          => Column::TYPE_DECIMAL,
                        'size'          => 10,
                        'scale'         => 8,
                        'notNull'       => false,
                    ]
                ),
                new Column(
                    'country_id',
                    [
                        'type'          => Column::TYPE_INTEGER,
                        'notNull'       => true,
                    ]
                ),
                new Column(
                    'user_added',
                    [
                        'type'          => Column::TYPE_TINYINTEGER,
                        'notNull'       => false,
                    ]
                )
            ],
            'options' => [
                'TABLE_COLLATION' => 'utf8mb4_general_ci'
            ]
        ];
    }

    public function indexes()
    {
        return
        [
            new Index(
                'column_name_index',
                [
                    'name'
                ],
                'INDEX'
            ),
            new Index(
                'column_country_id_index',
                [
                    'country_id'
                ],
                'INDEX'
            )
        ];
    }
}