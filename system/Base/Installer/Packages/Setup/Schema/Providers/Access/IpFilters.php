<?php

declare(strict_types=1);

/**
 * SP Framework
 *
 * @package     System\Base\Installer\Packages\Setup\Schema\Providers\Access
 * @copyright   Copyright (c) 2026
 * @link        https://github.com/sp-framework/core
 */

namespace System\Base\Installer\Packages\Setup\Schema\Providers\Access;

use Phalcon\Db\Column;
use Phalcon\Db\Index;

/**
 * Table schema definition for IpFilters (Providers Access Ip Filters).
 */
class IpFilters
{
    /**
     * Defines table columns and data types.
     *
     * @return array<string, array<int, Column>> Columns definition array.
     */
    public function columns(): array
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
                    'ip2location_proxy',//allow, block, -
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 20,
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

    /**
     * Defines table indexes and database constraints.
     *
     * @return array<int, Index> Indexes definition array.
     */
    public function indexes(): array
    {
        return
        [
            new Index(
                'column_INDEX',
                [
                    'app_id',
                    'address_type',
                    'address',
                    'ip2location_proxy',
                    'decimal',
                    'filter_type'
                ],
                'INDEX'
            )
        ];
    }
}
