<?php

namespace Apps\Dash\Packages\Business\Directory\Vendors\Install\Schema;

use Phalcon\Db\Column;
use Phalcon\Db\Index;

class Vendors
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
                    'logo',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 1024,
                        'notNull'       => false,
                    ]
                ),
                new Column(
                    'abn',
                    [
                        'type'          => Column::TYPE_BIGINTEGER,
                        'size'          => 11,
                        'notNull'       => true,
                    ]
                ),
                new Column(
                    'vendor_group_id',
                    [
                        'type'          => Column::TYPE_SMALLINTEGER,
                        'notNull'       => false,
                    ]
                ),
                new Column(
                    'business_name',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 200,
                        'notNull'       => true,
                    ]
                ),
                new Column(
                    'is_manufacturer',
                    [
                        'type'          => Column::TYPE_TINYINTEGER,
                        'notNull'       => false,
                    ]
                ),
                new Column(
                    'is_supplier',
                    [
                        'type'          => Column::TYPE_TINYINTEGER,
                        'notNull'       => false,
                    ]
                ),
                new Column(
                    'does_dropship',
                    [
                        'type'          => Column::TYPE_TINYINTEGER,
                        'notNull'       => false,
                    ]
                ),
                new Column(
                    'is_service_provider',
                    [
                        'type'          => Column::TYPE_TINYINTEGER,
                        'notNull'       => false,
                    ]
                ),
                new Column(
                    'does_jobwork',
                    [
                        'type'          => Column::TYPE_TINYINTEGER,
                        'notNull'       => false,
                    ]
                ),
                new Column(
                    'is_b2b_customer',
                    [
                        'type'          => Column::TYPE_TINYINTEGER,
                        'notNull'       => false,
                    ]
                ),
                new Column(
                    'b2b_account_managers',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 1024,
                        'notNull'       => false,
                    ]
                ),
                new Column(
                    'brands',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 1024,
                        'notNull'       => false,
                    ]
                ),
                new Column(
                    'product_count',
                    [
                        'type'          => Column::TYPE_INTEGER,
                        'notNull'       => false,
                    ]
                ),
                new Column(
                    'contact_phone',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 100,
                        'notNull'       => true,
                    ]
                ),
                new Column(
                    'contact_phone_ext',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 20,
                        'notNull'       => false,
                    ]
                ),
                new Column(
                    'contact_fax',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 100,
                        'notNull'       => false,
                    ]
                ),
                new Column(
                    'contact_other',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 200,
                        'notNull'       => false,
                    ]
                ),
                new Column(
                    'website',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 512,
                        'notNull'       => false,
                    ]
                ),
                new Column(
                    'email',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 100,
                        'notNull'       => true,
                    ]
                ),
            ],
            'options' => [
                'TABLE_COLLATION' => 'utf8mb4_general_ci'
            ],
            'indexes' => [
                new Index(
                    'column_UNIQUE',
                    [
                        'business_name'
                    ],
                    'UNIQUE'
                )
            ]
        ];
    }
}