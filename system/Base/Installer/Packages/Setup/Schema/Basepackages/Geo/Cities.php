<?php

namespace System\Base\Installer\Packages\Setup\Schema\Basepackages\Geo;

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
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 100,
                        'notNull'       => true,
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
                    'postcode',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 50,
                        'notNull'       => false,
                    ]
                ),
                new Column(
                    'state_id',
                    [
                        'type'          => Column::TYPE_INTEGER,
                        'notNull'       => true,
                    ]
                ),
                new Column(
                    'country_id',
                    [
                        'type'          => Column::TYPE_INTEGER,
                        'notNull'       => true,
                    ]
                )
            ]
        ];
    }

    public function indexes()
    {
        return
        [
            new Index(
                'column_INDEX',
                [
                    'name',
                    'state_id',
                    'country_id'
                ],
                'INDEX'
            )
        ];
    }
}