<?php

namespace System\Base\Installer\Packages\Setup\Schema\Basepackages\Geo\Regions;

use Phalcon\Db\Column;
use Phalcon\Db\Index;

class GeoRegions
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
                        'size'          => 50,
                        'notNull'       => true,
                    ]
                ),
                new Column(
                    'parent_region_id',
                    [
                        'type'          => Column::TYPE_INTEGER,
                        'notNull'       => false,
                    ]
                )
            ],
            'indexes' => [
                new Index(
                    'column_UNIQUE',
                    [
                        'name'
                    ],
                    'UNIQUE'
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
                'column_INDEX',
                [
                    'name'
                ],
                'INDEX'
            )
        ];
    }
}
