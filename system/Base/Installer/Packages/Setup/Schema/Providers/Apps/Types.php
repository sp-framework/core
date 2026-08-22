<?php

declare(strict_types=1);

/**
 * SP Framework
 *
 * @package     System\Base\Installer\Packages\Setup\Schema\Providers\Apps
 * @copyright   Copyright (c) 2026
 * @link        https://github.com/sp-framework/core
 */

namespace System\Base\Installer\Packages\Setup\Schema\Providers\Apps;

use Phalcon\Db\Column;
use Phalcon\Db\Index;

/**
 * Table schema definition for Types (Providers Apps Types).
 */
class Types
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
                        'type'          => Column::TYPE_TINYINTEGER,
                        'notNull'       => true,
                        'autoIncrement' => true,
                        'primary'       => true,
                    ]
                ),
                new Column(
                    'name',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 100,
                        'notNull' => true,
                    ]
                ),
                new Column(
                    'app_type',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 50,
                        'notNull' => true
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
                    'version',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 100,
                        'notNull' => true,
                    ]
                ),
                new Column(
                    'repo',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 2048,
                        'notNull' => true,
                    ]
                ),
                new Column(
                    'api_id',
                    [
                        'type'    => Column::TYPE_INTEGER,
                        'notNull' => true,
                    ]
                ),
                new Column(
                    'installed',
                    [
                        'type'    => Column::TYPE_TINYINTEGER,
                        'notNull' => true,
                    ]
                ),
                new Column(
                    'update_available',
                    [
                        'type'    => Column::TYPE_TINYINTEGER,
                        'size'    => 1,
                        'notNull' => false,
                    ]
                ),
                new Column(
                    'update_version',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 15,
                        'notNull' => false,
                    ]
                ),
                new Column(
                    'updated_by',
                    [
                        'type'    => Column::TYPE_INTEGER,
                        'notNull' => true,
                    ]
                ),
                new Column(
                    'updated_on',
                    [
                        'type'    => Column::TYPE_TIMESTAMP,
                        'notNull' => true,
                        'default' => 'CURRENT_TIMESTAMP',
                    ]
                ),
                new Column(
                    'level_of_update',
                    [
                        'type'    => Column::TYPE_INTEGER,
                        'notNull' => false,
                    ]
                ),
                new Column(
                    'auto_update',
                    [
                        'type'    => Column::TYPE_BOOLEAN,
                        'notNull' => false,
                    ]
                ),
                new Column(
                    'repo_details',
                    [
                        'type'    => Column::TYPE_JSON,
                        'notNull' => false,
                    ]
                )
            ],
            'indexes' => [
                new Index(
                    'column_UNIQUE',
                    [
                        'app_type',
                    ],
                    'UNIQUE'
                )
            ]
        ];
    }
}
