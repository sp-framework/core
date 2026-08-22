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
 * Table schema definition for Notifications (Basepackages Notifications).
 */
class Notifications
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
                        'package_name',
                        [
                            'type'    => Column::TYPE_VARCHAR,
                            'size'    => 100,
                            'notNull' => false
                        ]
                    ),
                    new Column(//Source Row Id
                        'package_row_id',
                        [
                            'type'    => Column::TYPE_INTEGER,
                            'notNull' => false
                        ]
                    ),
                    new Column(
                        'notification_type',
                        [
                            'type'    => Column::TYPE_TINYINTEGER,
                            'notNull' => true,
                            'default' => '0'
                        ]
                    ),
                    new Column(
                        'app_id',
                        [
                            'type'    => Column::TYPE_INTEGER,
                            'notNull' => true
                        ]
                    ),
                    new Column(
                        'account_id',
                        [
                            'type'    => Column::TYPE_INTEGER,
                            'notNull' => true
                        ]
                    ),
                    new Column(
                        'created_by',
                        [
                            'type'    => Column::TYPE_INTEGER,
                            'notNull' => true
                        ]
                    ),
                    new Column(
                        'created_at',
                        [
                            'type'    => Column::TYPE_TIMESTAMP,
                            'notNull' => true,
                            'default' => 'CURRENT_TIMESTAMP'
                        ]
                    ),
                    new Column(
                        'notification_title',
                        [
                            'type'    => Column::TYPE_VARCHAR,
                            'size'    => 4096,
                            'notNull' => true
                        ]
                    ),
                    new Column(
                        'notification_details',
                        [
                            'type'    => Column::TYPE_TEXT,
                            'notNull' => false
                        ]
                    ),
                    new Column(
                        'read',
                        [
                            'type'    => Column::TYPE_TINYINTEGER,
                            'notNull' => true,
                            'default' => '0'
                        ]
                    ),
                    new Column(
                        'archive',
                        [
                            'type'    => Column::TYPE_TINYINTEGER,
                            'notNull' => true,
                            'default' => '0'
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
                    'package_name',
                    'package_row_id',
                    'notification_type',
                    'account_id',
                    'read',
                    'archive'
                ],
                'INDEX'
            )
        ];
    }
}
