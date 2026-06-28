<?php

namespace System\Base\Installer\Packages\Setup\Schema\Providers\Access;

use Phalcon\Db\Column;
use Phalcon\Db\Index;

class IpFiltersIp2location
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
                    'address',//ipv4, ipv6 host or network address
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 100,
                        'notNull' => true,
                    ]
                ),
                new Column(
                    'decimal',//ip address to decimal for quick index search
                    [
                        'type'    => Column::TYPE_INTEGER,
                        'notNull' => false,
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
            ],
            'indexes' => [
                new Index(
                    'column_UNIQUE',
                    [
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
                    'address',
                    'decimal',
                    'country_code',
                    'region_name',
                    'city_name',
                    'is_proxy'
                ],
                'INDEX'
            )
        ];
    }
}