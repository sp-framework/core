<?php

namespace Apps\Dash\Packages\Business\Entities\Install\Schema;

use Phalcon\Db\Column;
use Phalcon\Db\Index;

class Entities
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
                    'entity_type',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 10,
                        'notNull'       => true,
                    ]
                ),
                new Column(
                    'address_id',
                    [
                        'type'          => Column::TYPE_INTEGER,
                        'notNull'       => true,
                    ]
                ),
                new Column(
                    'api_id',
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
                        'size'          => 1024,
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
                new Column(
                    'acn',
                    [
                        'type'          => Column::TYPE_INTEGER,
                        'notNull'       => true,
                    ]
                ),
                new Column(
                    'tfn',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 9,
                        'notNull'       => false,
                    ]
                ),
                new Column(
                    'currency',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 3,
                        'notNull'       => true,
                    ]
                ),
                new Column(
                    'bsb',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 8,
                        'notNull'       => false,
                    ]
                ),
                new Column(
                    'account_number',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 20,
                        'notNull'       => false,
                    ]
                ),
                new Column(
                    'swift_code',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 20,
                        'notNull'       => false,
                    ]
                ),
                new Column(
                    'accountant_vendor_id',
                    [
                        'type'          => Column::TYPE_INTEGER,
                        'notNull'       => false,
                    ]
                ),
                new Column(
                    'settings',
                    [
                        'type'          => Column::TYPE_TEXT,
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