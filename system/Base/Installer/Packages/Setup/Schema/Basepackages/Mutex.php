<?php

declare(strict_types=1);

/**
 * SP Framework
 *
 * @package     System\Base\Installer\Packages\Setup\Schema\Basepackages
 * @copyright   Copyright (c) 2026
 * @link        https://github.com/sp-framework/core
 */

namespace System\Base\Installer\Packages\Setup\Schema\Basepackages;

use Phalcon\Db\Column;
use Phalcon\Db\Index;

/**
 * Table schema definition for Mutex (Basepackages Mutex).
 */
class Mutex
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
                        'package_class',
                        [
                            'type'          => Column::TYPE_VARCHAR,
                            'size'          => 200,
                            'notNull'       => true
                        ]
                    ),
                    new Column(
                        'package_row_id',
                        [
                            'type'          => Column::TYPE_INTEGER,
                            'notNull'       => true
                        ]
                    ),
                    new Column(
                        'parent_lock_id',
                        [
                            'type'          => Column::TYPE_INTEGER,
                            'notNull'       => true,
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
                        'locked_at',
                        [
                            'type'          => Column::TYPE_INTEGER,
                            'notNull'       => true,
                        ]
                    ),
                ],
                'indexes' => [
                    new Index(
                        'column_UNIQUE',
                        [
                            'package_class',
                            'package_row_id',
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
                    'package_class',
                    'package_row_id',
                    'account_id',
                    'parent_lock_id'
                ],
                'INDEX'
            )
        ];
    }
}
