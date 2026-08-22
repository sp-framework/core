<?php

declare(strict_types=1);

/**
 * SP Framework
 *
 * @package     System\Base\Installer\Packages\Setup\Schema\Basepackages\Storages
 * @copyright   Copyright (c) 2026
 * @link        https://github.com/sp-framework/core
 */

namespace System\Base\Installer\Packages\Setup\Schema\Basepackages\Storages;

use Phalcon\Db\Column;
use Phalcon\Db\Index;

/**
 * Table schema definition for StoragesLocal (Basepackages Storages Storages Local).
 */
class StoragesLocal
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
                        'storages_id',
                        [
                            'type'    => Column::TYPE_TINYINTEGER,
                            'notNull' => true,
                        ]
                    ),
                    new Column(
                        'uuid',
                        [
                            'type'    => Column::TYPE_VARCHAR,
                            'size'    => 100,
                            'notNull' => true,
                        ]
                    ),
                    new Column(
                        'uuid_location',
                        [
                            'type'    => Column::TYPE_VARCHAR,
                            'size'    => 1024,
                            'notNull' => true,
                        ]
                    ),
                    new Column(
                        'links',
                        [
                            'type'    => Column::TYPE_VARCHAR,
                            'size'    => 1024,
                            'notNull' => false,
                        ]
                    ),
                    new Column(
                        'org_file_name',
                        [
                            'type'    => Column::TYPE_VARCHAR,
                            'size'    => 1024,
                            'notNull' => true,
                        ]
                    ),
                    new Column(
                        'size',
                        [
                            'type'      => Column::TYPE_INTEGER,
                            'notNull'   => true
                        ]
                    ),
                    new Column(
                        'height',
                        [
                            'type'      => Column::TYPE_INTEGER,
                            'notNull'   => false
                        ]
                    ),
                    new Column(
                        'width',
                        [
                            'type'      => Column::TYPE_INTEGER,
                            'notNull'   => false
                        ]
                    ),
                    new Column(
                        'type',
                        [
                            'type'    => Column::TYPE_VARCHAR,
                            'size'    => 1024,
                            'notNull' => true,
                        ]
                    ),
                    new Column(
                        'is_pointer',
                        [
                            'type'    => Column::TYPE_BOOLEAN,
                            'notNull' => true,
                        ]
                    ),
                    new Column(
                        'orphan',
                        [
                            'type'    => Column::TYPE_BOOLEAN,
                            'notNull' => true,
                        ]
                    ),
                    new Column(
                        'created_by',
                        [
                            'type'    => Column::TYPE_INTEGER,
                            'notNull' => false,
                        ]
                    ),
                    new Column(
                        'updated_by',
                        [
                            'type'    => Column::TYPE_INTEGER,
                            'notNull' => false,
                        ]
                    ),
                    new Column(
                        'created',
                        [
                            'type'    => Column::TYPE_TIMESTAMP,
                            'notNull' => true,
                            'default' => 'CURRENT_TIMESTAMP',
                        ]
                    ),
                    new Column(
                        'updated',
                        [
                            'type'    => Column::TYPE_TIMESTAMP,
                            'notNull' => true,
                            'default' => 'CURRENT_TIMESTAMP',
                        ]
                    ),
                    new Column(
                        'package_class',
                        [
                            'type'    => Column::TYPE_VARCHAR,
                            'size'    => 200,
                            'notNull' => true
                        ]
                    ),
                    new Column(//Source Row Id
                        'package_row_id',
                        [
                            'type'    => Column::TYPE_INTEGER,
                            'notNull' => true
                        ]
                    ),
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
                    'uuid',
                    'orphan',
                    'package_class',
                    'package_row_id'
                ],
                'INDEX'
            )
        ];
    }
}
