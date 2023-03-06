<?php

namespace System\Base\Installer\Packages\Setup\Schema\Apps;

use Phalcon\Db\Column;

class IpFilter
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
                    'app_id',
                    [
                        'type'    => Column::TYPE_TINYINTEGER,
                        'notNull' => true,
                    ]
                ),
                new Column(
                    'ip_address',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 100,
                        'notNull' => true,
                    ]
                ),
                new Column(
                    'address_type',//1 - Host, 2 - Network
                    [
                        'type'    => Column::TYPE_TINYINTEGER,
                        'notNull' => true,
                    ]
                ),
                new Column(
                    'filter_type',//1 - Allow, 2 - Block, 3 - Monitor (for failed login attempts)
                    [
                        'type'    => Column::TYPE_TINYINTEGER,
                        'notNull' => true,
                    ]
                ),
                new Column(
                    'added_by',//0 - Auth_Service, account_id
                    [
                        'type'    => Column::TYPE_INTEGER,
                        'notNull' => true,
                    ]
                ),
                new Column(
                    'hit_count',//Hit count on filter_type 1 or 2
                    [
                        'type'    => Column::TYPE_INTEGER,
                        'notNull' => false,
                    ]
                ),
                new Column(
                    'incorrect_attempts',//for filter_type 3
                    [
                        'type'    => Column::TYPE_TINYINTEGER,
                        'notNull' => false,
                    ]
                )
            ],
            'indexes' => [
                new Index(
                    'column_UNIQUE',
                    [
                        'app_id',
                        'ip_address'
                    ],
                    'UNIQUE'
                )
            ],
            'options' => [
                'TABLE_COLLATION' => 'utf8mb4_general_ci'
            ]
        ];
    }
}