<?php

namespace Applications\Dash\Packages\Ims\Suppliers\Install\Schema;

use Phalcon\Db\Column;

class Suppliers
{
    public function columns()
    {
        return
        [
           'columns' => [
                new Column(
                    'id',
                    [
                        'type'          => Column::TYPE_TINYINTEGER,
                        'notNull'       => true,
                        'autoIncrement' => true,
                        'primary'       => true,
                    ]
                ),
                new Column(
                    'logo',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 1024,
                        'notNull' => false,
                    ]
                ),
                new Column(
                    'abn',
                    [
                        'type'    => Column::TYPE_BIGINTEGER,
                        'size'    => 11,
                        'notNull' => false,
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
                    'type',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 25,
                        'notNull' => true,
                    ]
                ),
                new Column(
                    'is_manufacturer',
                    [
                        'type'    => Column::TYPE_TINYINTEGER,
                        'notNull' => true,
                    ]
                ),
                new Column(
                    'does_dropship',
                    [
                        'type'    => Column::TYPE_TINYINTEGER,
                        'notNull' => true,
                    ]
                ),
                new Column(
                    'brands',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 1024,
                        'notNull' => true,
                    ]
                ),
                new Column(
                    'contact_first_name',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 100,
                        'notNull' => true,
                    ]
                ),
                new Column(
                    'contact_last_name',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 100,
                        'notNull' => true,
                    ]
                ),
                new Column(
                    'contact_phone',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 25,
                        'notNull' => true,
                    ]
                ),
                new Column(
                    'contact_email',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 100,
                        'notNull' => true,
                    ]
                ),
                // new Column(
                //     'street_address',
                //     [
                //         'type'    => Column::TYPE_VARCHAR,
                //         'size'    => 100,
                //         'notNull' => true,
                //     ]
                // ),
                // new Column(
                //     'street_address_2',
                //     [
                //         'type'    => Column::TYPE_VARCHAR,
                //         'size'    => 100,
                //         'notNull' => false,
                //     ]
                // ),
                // new Column(
                //     'city_id',
                //     [
                //         'type'    => Column::TYPE_INTEGER,
                //         'notNull' => true,
                //     ]
                // ),
                // new Column(
                //     'city_name',
                //     [
                //         'type'    => Column::TYPE_VARCHAR,
                //         'size'    => 255,
                //         'notNull' => true,
                //     ]
                // ),
                // new Column(
                //     'post_code',
                //     [
                //         'type'    => Column::TYPE_INTEGER,
                //         'size'    => 20,
                //         'notNull' => true,
                //     ]
                // ),
                // new Column(
                //     'state_id',
                //     [
                //         'type'    => Column::TYPE_INTEGER,
                //         'notNull' => true,
                //     ]
                // ),
                // new Column(
                //     'state_name',
                //     [
                //         'type'    => Column::TYPE_VARCHAR,
                //         'size'    => 255,
                //         'notNull' => true,
                //     ]
                // ),
                // new Column(
                //     'country_id',
                //     [
                //         'type'    => Column::TYPE_INTEGER,
                //         'notNull' => true,
                //     ]
                // ),
                // new Column(
                //     'country_name',
                //     [
                //         'type'    => Column::TYPE_VARCHAR,
                //         'size'    => 255,
                //         'notNull' => true,
                //     ]
                // ),
                new Column(
                    'address_id',
                    [
                        'type'    => Column::TYPE_INTEGER,
                        'notNull' => true,
                    ]
                ),
                new Column(
                    'internal_notes',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 4096,
                        'notNull' => false,
                    ]
                ),
            ],
            'options' => [
                'TABLE_COLLATION' => 'utf8mb4_unicode_ci'
            ]
        ];
    }
}