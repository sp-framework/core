<?php

namespace System\Base\Installer\Packages\Setup\Schema\Providers\Access;

use Phalcon\Db\Column;
use Phalcon\Db\Index;

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
                        'type'    => Column::TYPE_INTEGER,
                        'notNull' => true,
                    ]
                ),
                new Column(
                    'address_type',//host, network, ip2location
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 20,
                        'notNull' => true,
                    ]
                ),
                new Column(
                    'address',//ipv4, ipv6 host or network address
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 100,
                        'notNull' => true,
                    ]
                ),
                new Column(
                    'country_code',//ip2location country code - AU
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 10,
                        'notNull' => false,
                    ]
                ),
                new Column(
                    'region_name',//ip2location region name - Victoria
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 100,
                        'notNull' => false,
                    ]
                ),
                new Column(
                    'city_name',//ip2location city name - Melbourne
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 100,
                        'notNull' => false,
                    ]
                ),
                new Column(
                    'is_proxy',//ip2location proxy
                    [
                        'type'    => Column::TYPE_BOOLEAN,
                        'notNull' => false,
                    ]
                ),
                new Column(
                    'proxy_type',//ip2location proxy type
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 100,
                        'notNull' => false,
                    ]
                ),
                new Column(
                    'filter_type',//allow, block, monitor
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 20,
                        'notNull' => true,
                    ]
                ),
                new Column(//Self parent for network
                    'parent_id',
                    [
                        'type'    => Column::TYPE_INTEGER,
                        'notNull' => false,
                    ]
                ),
                new Column(
                    'hit_count',
                    [
                        'type'    => Column::TYPE_INTEGER,
                        'notNull' => false,
                    ]
                ),
                new Column(
                    'incorrect_login_attempts',
                    [
                        'type'    => Column::TYPE_TINYINTEGER,
                        'notNull' => false,
                    ]
                ),
                new Column(
                    'updated_by',//0 - Auth_Service, account_id
                    [
                        'type'    => Column::TYPE_INTEGER,
                        'notNull' => true,
                    ]
                ),
                new Column(
                    'updated_at',//for auto unblock
                    [
                        'type'    => Column::TYPE_INTEGER,
                        'notNull' => false,
                    ]
                )
            ],
            'indexes' => [
                new Index(
                    'column_UNIQUE',
                    [
                        'app_id',
                        'address'
                    ],
                    'UNIQUE'
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
                    'app_id',
                    'address_type',
                    'address',
                    'filter_type',
                    'country_code',
                    'region_name',
                    'city_name'
                ],
                'INDEX'
            )
        ];
    }
}