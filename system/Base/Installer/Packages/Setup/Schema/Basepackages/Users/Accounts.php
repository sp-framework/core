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
 * Table schema definition for Accounts (Basepackages Users Accounts).
 */
class Accounts
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
                        'status',
                        [
                            'type'          => Column::TYPE_BOOLEAN,
                            'notNull'       => true,
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
                        'username',
                        [
                            'type'          => Column::TYPE_VARCHAR,
                            'size'          => 50,
                            'notNull'       => true,
                        ]
                    ),
                    new Column(
                        'domain',
                        [
                            'type'          => Column::TYPE_VARCHAR,
                            'size'          => 50,
                            'notNull'       => true,
                        ]
                    ),
                    new Column(
                        'profile_package_class',
                        [
                            'type'          => Column::TYPE_VARCHAR,
                            'size'          => 200,
                            'notNull'       => true
                        ]
                    ),
                    new Column(
                        'profile_package_row_id',
                        [
                            'type'          => Column::TYPE_INTEGER,
                            'notNull'       => true
                        ]
                    ),
                ],
                'indexes' => [
                    new Index(
                        'column_UNIQUE',
                        [
                            'email',
                            'username'
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
                    'email',
                    'domain',
                    'username'
                ],
                'INDEX'
            )
        ];
    }
}
