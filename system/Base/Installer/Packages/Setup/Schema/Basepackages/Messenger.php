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

/**
 * Table schema definition for Messenger (Basepackages Messenger).
 */
class Messenger
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
                    'from_account_id',
                    [
                        'type'    => Column::TYPE_INTEGER,
                        'notNull' => true,
                    ]
                ),
                new Column(
                    'to_account_id',
                    [
                        'type'    => Column::TYPE_INTEGER,
                        'notNull' => true,
                    ]
                ),
                new Column(
                    'message',
                    [
                        'type'    => Column::TYPE_TEXT,
                        'notNull' => true,
                    ]
                ),
                new Column(
                    'read',
                    [
                        'type'    => Column::TYPE_TINYINTEGER,
                        'notNull' => false
                    ]
                ),
                new Column(
                    'edited',
                    [
                        'type'    => Column::TYPE_TINYINTEGER,
                        'notNull' => false
                    ]
                ),
                new Column(
                    'removed',
                    [
                        'type'    => Column::TYPE_TINYINTEGER,
                        'notNull' => false
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
                    'updated_at',
                    [
                        'type'    => Column::TYPE_TIMESTAMP,
                        'notNull' => true,
                        'default' => 'CURRENT_TIMESTAMP'
                    ]
                ),
            ]
        ];
    }
}
