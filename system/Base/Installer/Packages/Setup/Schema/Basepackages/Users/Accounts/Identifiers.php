<?php

declare(strict_types=1);

/**
 * SP Framework
 *
 * @package     System\Base\Installer\Packages\Setup\Schema\Basepackages\Users\Accounts
 * @copyright   Copyright (c) 2026
 * @link        https://github.com/sp-framework/core
 */

namespace System\Base\Installer\Packages\Setup\Schema\Basepackages\Users\Accounts;

use Phalcon\Db\Column;
use Phalcon\Db\Index;

/**
 * Table schema definition for Identifiers (Basepackages Users Accounts Identifiers).
 */
class Identifiers
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
                        'app',
                        [
                            'type'          => Column::TYPE_VARCHAR,
                            'size'          => 50,
                            'notNull'       => true,
                        ]
                    ),
                    new Column(
                        'session_id',
                        [
                            'type'          => Column::TYPE_VARCHAR,
                            'size'          => 50,
                            'notNull'       => true,
                        ]
                    ),
                    new Column(
                        'identifier',
                        [
                            'type'          => Column::TYPE_VARCHAR,
                            'size'          => 200,
                            'notNull'       => true,
                        ]
                    ),
                    new Column(
                        'token',
                        [
                            'type'          => Column::TYPE_VARCHAR,
                            'size'          => 200,
                            'notNull'       => true,
                        ]
                    )
                ],
                'indexes' => [
                    new Index(
                        'column_UNIQUE',
                        [
                            'session_id',
                            'app'
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
