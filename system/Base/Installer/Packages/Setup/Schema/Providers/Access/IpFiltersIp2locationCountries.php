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
 * Table schema definition for IpFiltersIp2locationCountries (Providers Access Ip Filters Ip2location Countries).
 */
class IpFiltersIp2locationCountries
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
                    'name',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 255,
                        'notNull'       => true
                    ]
                ),
                new Column(
                    'iso2',
                    [
                        'type'          => Column::TYPE_CHAR,
                        'size'          => 2,
                        'notNull'       => false
                    ]
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
                'column_UNIQUE',
                [
                    'name',
                    'iso2'
                ],
                'UNIQUE'
            ),
            new Index(
                'column_INDEX',
                [
                    'name',
                    'iso2'
                ],
                'INDEX'
            )
        ];
    }
}
