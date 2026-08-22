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
 * Table schema definition for Widgets (Basepackages Widgets).
 */
class Widgets
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
                    'method',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 255,
                        'notNull' => true,
                    ]
                ),
                new Column(
                    'component_id',
                    [
                        'type'    => Column::TYPE_SMALLINTEGER,
                        'notNull' => true
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
                    'multiple',
                    [
                        'type'    => Column::TYPE_BOOLEAN,
                        'notNull' => true
                    ]
                ),
                new Column(
                    'max_multiple',
                    [
                        'type'    => Column::TYPE_TINYINTEGER,
                        'notNull' => false
                    ]
                ),
                new Column(
                    'settings',
                    [
                        'type'    => Column::TYPE_JSON,
                        'notNull' => true
                    ]
                )
            ],
            'indexes' => [
                new Index(
                    'column_UNIQUE',
                    [
                        'method',
                        'app_type'
                    ],
                    'UNIQUE'
                )
            ]
        ];
    }
}
