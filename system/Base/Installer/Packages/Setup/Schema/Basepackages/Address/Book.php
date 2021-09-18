<?php

namespace System\Base\Installer\Packages\Setup\Schema\Basepackages\Address;

use Phalcon\Db\Column;

class Book
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
                    'address_type',
                    [
                        'type'    => Column::TYPE_TINYINTEGER,
                        'notNull' => true
                    ]
                ),
                new Column(
                    'is_primary',
                    [
                        'type'    => Column::TYPE_TINYINTEGER,
                        'notNull' => true
                    ]
                ),
                new Column(
                    'package_name',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 100,
                        'notNull' => true,
                    ]
                ),
                new Column(
                    'attention_to',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 100,
                        'notNull' => false,
                    ]
                ),
                new Column(
                    'street_address',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 100,
                        'notNull' => false,
                    ]
                ),
                new Column(
                    'street_address_2',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 100,
                        'notNull' => false,
                    ]
                ),
                new Column(
                    'city_id',
                    [
                        'type'    => Column::TYPE_INTEGER,
                        'notNull' => false,
                    ]
                ),
                new Column(
                    'city_name',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 255,
                        'notNull' => false,
                    ]
                ),
                new Column(
                    'post_code',
                    [
                        'type'    => Column::TYPE_INTEGER,
                        'size'    => 20,
                        'notNull' => false,
                    ]
                ),
                new Column(
                    'state_id',
                    [
                        'type'    => Column::TYPE_INTEGER,
                        'notNull' => false,
                    ]
                ),
                new Column(
                    'state_name',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 255,
                        'notNull' => false,
                    ]
                ),
                new Column(
                    'country_id',
                    [
                        'type'    => Column::TYPE_INTEGER,
                        'notNull' => false,
                    ]
                ),
                new Column(
                    'country_name',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 255,
                        'notNull' => false,
                    ]
                ),
            ],
            'options' => [
                'TABLE_COLLATION' => 'utf8mb4_general_ci'
            ]
        ];
    }
}