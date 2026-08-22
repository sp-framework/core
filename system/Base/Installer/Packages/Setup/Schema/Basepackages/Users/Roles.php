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
 * Table schema definition for Roles (Basepackages Users Roles).
 */
class Roles
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
                        'description',
                        [
                            'type'    => Column::TYPE_VARCHAR,
                            'size'    => 2048,
                            'notNull' => false,
                        ]
                    ),
                    new Column(
                        'type',//0 for system and 1 for user.
                        [
                            'type'    => Column::TYPE_BOOLEAN,
                            'notNull' => true,
                        ]
                    ),
                    new Column(
                        'permissions',
                        [
                            'type'    => Column::TYPE_JSON,
                            'notNull' => true,
                        ]
                    )
                ],
                'indexes' => [
                    new Index(
                        'column_UNIQUE',
                        [
                            'name',
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
