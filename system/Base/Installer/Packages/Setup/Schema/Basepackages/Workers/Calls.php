<?php

declare(strict_types=1);

/**
 * SP Framework
 *
 * @package     System\Base\Installer\Packages\Setup\Schema\Basepackages\Workers
 * @copyright   Copyright (c) 2026
 * @link        https://github.com/sp-framework/core
 */

namespace System\Base\Installer\Packages\Setup\Schema\Basepackages\Workers;

use Phalcon\Db\Column;
use Phalcon\Db\Index;

/**
 * Table schema definition for Calls (Basepackages Workers Calls).
 */
class Calls
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
                            'type'          => Column::TYPE_SMALLINTEGER,
                            'notNull'       => true,
                            'autoIncrement' => true,
                            'primary'       => true,
                        ]
                    ),
                    new Column(
                        'name',
                        [
                            'type'          => Column::TYPE_VARCHAR,
                            'size'          => 100,
                            'notNull'       => true,
                        ]
                    ),
                    new Column(
                        'display_name',
                        [
                            'type'          => Column::TYPE_VARCHAR,
                            'size'          => 255,
                            'notNull'       => true,
                        ]
                    ),
                    new Column(
                        'description',
                        [
                            'type'          => Column::TYPE_VARCHAR,
                            'size'          => 2048,
                            'notNull'       => false,
                        ]
                    ),
                    new Column(
                        'class',
                        [
                            'type'          => Column::TYPE_VARCHAR,
                            'size'          => 512,
                            'notNull'       => true,
                        ]
                    ),
                    new Column(
                        'can_be_scheduled',
                        [
                            'type'          => Column::TYPE_BOOLEAN,
                            'notNull'       => true,
                        ]
                    ),
                    new Column(
                        'can_be_run_on_demand',
                        [
                            'type'          => Column::TYPE_BOOLEAN,
                            'notNull'       => true,
                        ]
                    ),
                    new Column(
                        'package_id',
                        [
                            'type'          => Column::TYPE_INTEGER,
                            'notNull'       => true,
                        ]
                    )
                ],
                'indexes' => [
                    new Index(
                        'column_UNIQUE',
                        [
                            'class'
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
                    'name'
                ],
                'INDEX'
            )
        ];
    }
}
