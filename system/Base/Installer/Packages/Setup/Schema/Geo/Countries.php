<?php

namespace System\Base\Installer\Packages\Setup\Schema\Geo;

use Phalcon\Db\Column;

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
                        'type'      => Column::TYPE_VARCHAR,
                        'size'      => 255,
                        'notNull'   => true
                    ]
                ),
                new Column(
                    'iso3',
                    [
                        'type'      => Column::TYPE_CHAR,
                        'size'      => 3,
                        'notNull'   => false
                    ]
                ),
                new Column(
                    'iso2',
                    [
                        'type'      => Column::TYPE_CHAR,
                        'size'      => 2,
                        'notNull'   => false
                    ]
                ),
                new Column(
                    'phone_code',
                    [
                        'type'      => Column::TYPE_VARCHAR,
                        'size'      => 255,
                        'notNull'   => false
                    ]
                ),
                new Column(
                    'capital',
                    [
                        'type'      => Column::TYPE_VARCHAR,
                        'size'      => 255,
                        'notNull'   => false
                    ]
                ),
                new Column(
                    'currency',
                    [
                        'type'      => Column::TYPE_VARCHAR,
                        'size'      => 255,
                        'notNull'   => false
                    ]
                ),
                new Column(
                    'currency_symbol',
                    [
                        'type'      => Column::TYPE_VARCHAR,
                        'size'      => 10,
                        'notNull'   => false
                    ]
                ),
                new Column(
                    'currency_enabled',
                    [
                        'type'    => Column::TYPE_TINYINTEGER,
                        'notNull' => false,
                    ]
                ),
                new Column(
                    'native',
                    [
                        'type'      => Column::TYPE_VARCHAR,
                        'size'      => 255,
                        'notNull'   => false
                    ]
                ),
                new Column(
                    'region',
                    [
                        'type'      => Column::TYPE_VARCHAR,
                        'size'      => 255,
                        'notNull'   => false
                    ]
                ),
                new Column(
                    'subregion',
                    [
                        'type'      => Column::TYPE_VARCHAR,
                        'size'      => 255,
                        'notNull'   => false
                    ]
                ),
                new Column(
                    'emoji',
                    [
                        'type'      => Column::TYPE_VARCHAR,
                        'size'      => 255,
                        'notNull'   => false
                    ]
                ),
                new Column(
                    'emojiU',
                    [
                        'type'      => Column::TYPE_VARCHAR,
                        'size'      => 255,
                        'notNull'   => false
                    ]
                ),
                new Column(
                    'installed',
                    [
                        'type'    => Column::TYPE_TINYINTEGER,
                        'notNull' => true,
                    ]
                ),
                new Column(
                    'enabled',
                    [
                        'type'    => Column::TYPE_TINYINTEGER,
                        'notNull' => true,
                    ]
                ),
                new Column(
                    'cached',
                    [
                        'type'    => Column::TYPE_TINYINTEGER,
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