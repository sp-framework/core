<?php

declare(strict_types=1);

/**
 * SP Framework
 *
 * @package     System\Base\Installer\Packages\Setup\Schema\Basepackages\Email
 * @copyright   Copyright (c) 2026
 * @link        https://github.com/sp-framework/core
 */

namespace System\Base\Installer\Packages\Setup\Schema\Basepackages\Email;

use Phalcon\Db\Column;
use Phalcon\Db\Index;

/**
 * Table schema definition for Services (Basepackages Email Services).
 */
class Services
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
                        'description',
                        [
                            'type'    => Column::TYPE_VARCHAR,
                            'size'    => 2048,
                            'notNull' => false
                        ]
                    ),
                    new Column(
                        'host',
                        [
                            'type'    => Column::TYPE_VARCHAR,
                            'size'    => 100,
                            'notNull' => true,
                        ]
                    ),
                    new Column(
                        'port',
                        [
                            'type'    => Column::TYPE_MEDIUMINTEGER,
                            'notNull' => true,
                        ]
                    ),
                    new Column(
                        'auth',
                        [
                            'type'    => Column::TYPE_TINYINTEGER,
                            'notNull' => false,
                        ]
                    ),
                    new Column(
                        'username',
                        [
                            'type'    => Column::TYPE_VARCHAR,
                            'size'    => 100,
                            'notNull' => false,
                        ]
                    ),
                    new Column(
                        'password',
                        [
                            'type'    => Column::TYPE_VARCHAR,
                            'size'    => 1024,
                            'notNull' => false,
                        ]
                    ),
                    new Column(
                        'from_address',
                        [
                            'type'    => Column::TYPE_VARCHAR,
                            'size'    => 100,
                            'notNull' => true,
                        ]
                    ),
                    new Column(
                        'from_name',
                        [
                            'type'    => Column::TYPE_VARCHAR,
                            'size'    => 100,
                            'notNull' => false,
                        ]
                    ),
                    new Column(
                        'encryption',
                        [
                            'type'    => Column::TYPE_TINYINTEGER,
                            'notNull' => false,
                        ]
                    ),
                    new Column(
                        'allow_html_body',
                        [
                            'type'    => Column::TYPE_TINYINTEGER,
                            'notNull' => false,
                        ]
                    )
                ],
               'indexes' => [
                    new Index(
                        'column_UNIQUE',
                        [
                            'host',
                            'port',
                            'username'
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
                    'host'
                ],
                'INDEX'
            )
        ];
    }
}
