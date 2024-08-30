<?php

namespace System\Base\Installer\Packages\Setup\Schema\Basepackages\Geo;

use Phalcon\Db\Column;

class Timezones
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
                    'zone_name',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 512,
                        'notNull'       => false
                    ]
                ),
                new Column(
                    'tz_name',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 255,
                        'notNull'       => false
                    ]
                ),
                new Column(
                    'gmt_offset',
                    [
                        'type'          => Column::TYPE_INTEGER,
                        'notNull'       => false
                    ]
                ),
                new Column(
                    'gmt_offset_name',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 255,
                        'notNull'       => false
                    ]
                ),
                new Column(
                    'abbreviation',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 10,
                        'notNull'       => false
                    ]
                ),
                new Column(
                    'gmt_offset_dst',
                    [
                        'type'          => Column::TYPE_INTEGER,
                        'notNull'       => false
                    ]
                ),
                new Column(
                    'gmt_offset_name_dst',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 255,
                        'notNull'       => false
                    ]
                ),
                new Column(
                    'abbreviation_dst',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 10,
                        'notNull'       => false
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
}