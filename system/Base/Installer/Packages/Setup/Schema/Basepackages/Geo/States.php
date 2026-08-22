<?php

declare(strict_types=1);

/**
 * SP Framework
 *
 * @package     System\Base\Installer\Packages\Setup\Schema\Basepackages\Geo
 * @copyright   Copyright (c) 2026
 * @link        https://github.com/sp-framework/core
 */

namespace System\Base\Installer\Packages\Setup\Schema\Basepackages\Geo;

use Phalcon\Db\Column;
use Phalcon\Db\Index;

/**
 * Table schema definition for States (Basepackages Geo States).
 */
class States
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
                        'notNull'       => true,
                    ]
                ),
                new Column(
                    'longitude',
                    [
                        'type'          => Column::TYPE_DECIMAL,
                        'size'          => 11,
                        'scale'         => 8,
                        'notNull'       => false,
                    ]
                ),
                new Column(
                    'latitude',
                    [
                        'type'          => Column::TYPE_DECIMAL,
                        'size'          => 10,
                        'scale'         => 8,
                        'notNull'       => false,
                    ]
                ),
                new Column(
                    'country_id',
                    [
                        'type'          => Column::TYPE_INTEGER,
                        'notNull'       => true,
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
                'column_INDEX',
                [
                    'name',
                    'country_id'
                ],
                'INDEX'
            )
        ];
    }
}
