<?php

declare(strict_types=1);

/**
 * SP Framework
 *
 * @package     System\Base\Installer\Packages\Setup\Schema\Basepackages\Users
 * @copyright   Copyright (c) 2026
 * @link        https://github.com/sp-framework/core
 */

namespace System\Base\Installer\Packages\Setup\Schema\Basepackages\Users;

use Phalcon\Db\Column;
use Phalcon\Db\Index;

/**
 * Table schema definition for Profiles (Basepackages Users Profiles).
 */
class Profiles
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
                        'account_id',
                        [
                            'type'          => Column::TYPE_INTEGER,
                            'notNull'       => true,
                        ]
                    ),
                    new Column(
                        'locale_country_iso3',
                        [
                            'type'          => Column::TYPE_VARCHAR,
                            'size'          => 3,
                            'notNull'       => false,
                        ]
                    ),
                    new Column(
                        'locale_timezone',
                        [
                            'type'          => Column::TYPE_VARCHAR,
                            'size'          => 50,
                            'notNull'       => false,
                        ]
                    ),
                    new Column(
                        'settings',
                        [
                            'type'          => Column::TYPE_JSON,
                            'notNull'       => false,
                        ]
                    )
                ],
                'indexes' => [
                    new Index(
                        'column_UNIQUE',
                        [
                            'account_id'
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
                    'account_id'
                ],
                'INDEX'
            )
        ];
    }
}
