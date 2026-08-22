<?php

declare(strict_types=1);

/**
 * SP Framework
 *
 * @package     System\Base\Installer\Packages\Setup\Schema\Modules
 * @copyright   Copyright (c) 2026
 * @link        https://github.com/sp-framework/core
 */

namespace System\Base\Installer\Packages\Setup\Schema\Modules;

use Phalcon\Db\Column;

/**
 * Table schema definition for Queues (Modules Queues).
 */
class Queues
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
                            'type'          => Column::TYPE_MEDIUMINTEGER,
                            'notNull'       => true,
                            'autoIncrement' => true,
                            'primary'       => true,
                        ]
                    ),
                    new Column(
                        'status',
                        [
                            'type'    => Column::TYPE_TINYINTEGER,
                            'notNull' => true,
                        ]
                    ),
                    new Column(
                        'prechecked_at',
                        [
                            'type'    => Column::TYPE_VARCHAR,
                            'size'    => 100,
                            'notNull' => false,
                        ]
                    ),
                    new Column(
                        'prechecked_by',
                        [
                            'type'    => Column::TYPE_INTEGER,
                            'notNull' => false,
                        ]
                    ),
                    new Column(
                        'processed_at',
                        [
                            'type'    => Column::TYPE_VARCHAR,
                            'size'    => 100,
                            'notNull' => false,
                        ]
                    ),
                    new Column(
                        'processed_by',
                        [
                            'type'    => Column::TYPE_INTEGER,
                            'notNull' => false,
                        ]
                    ),
                    new Column(
                        'results',
                        [
                            'type'    => Column::TYPE_JSON,
                            'notNull' => false,
                        ]
                    ),
                    new Column(
                        'tasks',
                        [
                            'type'    => Column::TYPE_JSON,
                            'notNull' => false,
                        ]
                    ),
                    new Column(
                        'tasks_count',
                        [
                            'type'    => Column::TYPE_JSON,
                            'notNull' => false,
                        ]
                    ),
                    new Column(
                        'total',
                        [
                            'type'    => Column::TYPE_SMALLINTEGER,
                            'notNull' => false,
                        ]
                    ),
                    new Column(
                        'settings',
                        [
                            'type'    => Column::TYPE_JSON,
                            'notNull' => false,
                        ]
                    )
                ]
            ];
    }
}
