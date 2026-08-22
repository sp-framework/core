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
 * Table schema definition for Filters (Basepackages Filters).
 */
class Filters
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
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 50,
                        'notNull' => true,
                    ]
                ),
                new Column(
                    'app_type',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 50,
                        'notNull' => true,
                    ]
                ),
                new Column(
                    'component_id',
                    [
                        'type'    => Column::TYPE_SMALLINTEGER,
                        'size'    => 1,
                        'notNull' => true,
                    ]
                ),
                new Column(
                    'conditions',
                    [
                        'type'    => Column::TYPE_TEXT,
                        'notNull' => false,
                    ]
                ),
                new Column(
                    'filter_type',
                    [
                        'type'    => Column::TYPE_TINYINTEGER,
                        'size'    => 1,
                        'notNull' => true,
                    ]
                ),
                new Column(
                    'auto_generated',
                    [
                        'type'    => Column::TYPE_TINYINTEGER,
                        'size'    => 1,
                        'notNull' => false,
                    ]
                ),
                new Column(
                    'is_default',
                    [
                        'type'    => Column::TYPE_TINYINTEGER,
                        'size'    => 1,
                        'notNull' => true,
                    ]
                ),
                new Column(
                    'account_id',
                    [
                        'type'    => Column::TYPE_INTEGER,
                        'notNull' => true,
                    ]
                ),
                new Column(
                    'shared_ids',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 4096,
                        'notNull' => false,
                    ]
                ),
                new Column(
                    'archived',
                    [
                        'type'    => Column::TYPE_BOOLEAN,
                        'notNull' => true,
                    ]
                ),
            ],
           'indexes' => [
                new Index(
                    'column_UNIQUE',
                    [
                        'name',
                        'app_type',
                        'account_id',
                        'component_id'
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
                    'app_type',
                    'account_id',
                    'component_id',
                    'archived'
                ],
                'INDEX'
            )
        ];
    }
}
