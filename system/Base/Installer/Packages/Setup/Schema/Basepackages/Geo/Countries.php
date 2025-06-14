<?php

namespace System\Base\Installer\Packages\Setup\Schema\Basepackages\Geo;

use Phalcon\Db\Column;
use Phalcon\Db\Index;

class Countries
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
                        'notNull'       => true
                    ]
                ),
                new Column(
                    'native',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 255,
                        'notNull'       => false
                    ]
                ),
                new Column(
                    'nationality',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 255,
                        'notNull'       => false
                    ]
                ),
                new Column(
                    'capital',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 255,
                        'notNull'       => false
                    ]
                ),
                new Column(
                    'iso2',
                    [
                        'type'          => Column::TYPE_CHAR,
                        'size'          => 2,
                        'notNull'       => false
                    ]
                ),
                new Column(
                    'iso3',
                    [
                        'type'          => Column::TYPE_CHAR,
                        'size'          => 3,
                        'notNull'       => false
                    ]
                ),
                new Column(
                    'currency',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 255,
                        'notNull'       => false
                    ]
                ),
                new Column(
                    'currency_name',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 255,
                        'notNull'       => false
                    ]
                ),
                new Column(
                    'currency_symbol',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 10,
                        'notNull'       => false
                    ]
                ),
                new Column(
                    'currency_enabled',
                    [
                        'type'          => Column::TYPE_TINYINTEGER,
                        'notNull'       => false,
                    ]
                ),
                new Column(
                    'region_id',
                    [
                        'type'          => Column::TYPE_SMALLINTEGER,
                        'notNull'       => false
                    ]
                ),
                new Column(
                    'region',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 255,
                        'notNull'       => false
                    ]
                ),
                new Column(
                    'subregion_id',
                    [
                        'type'          => Column::TYPE_SMALLINTEGER,
                        'notNull'       => false
                    ]
                ),
                new Column(
                    'subregion',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 255,
                        'notNull'       => false
                    ]
                ),
                new Column(
                    'numeric_code',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 10,
                        'notNull'       => false
                    ]
                ),
                new Column(
                    'phone_code',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 10,
                        'notNull'       => false
                    ]
                ),
                new Column(
                    'tld',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 100,
                        'notNull'       => false
                    ]
                ),
                new Column(
                    'emoji',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 255,
                        'notNull'       => false
                    ]
                ),
                new Column(
                    'emojiU',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 255,
                        'notNull'       => false
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
                    'translations',
                    [
                        'type'          => Column::TYPE_JSON,
                        'size'          => 2048,
                        'notNull'       => false
                    ]
                ),
                new Column(
                    'installed',
                    [
                        'type'          => Column::TYPE_TINYINTEGER,
                        'notNull'       => true,
                    ]
                ),
                new Column(
                    'enabled',
                    [
                        'type'          => Column::TYPE_TINYINTEGER,
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
            ]
        ];
    }

    public function indexes()
    {
        return
        [
            new Index(
                'column_UNIQUE',
                [
                    'name'
                ],
                'UNIQUE'
            ),
            new Index(
                'column_INDEX',
                [
                    'name',
                    'iso2',
                    'iso3',
                ],
                'INDEX'
            )
        ];
    }
}