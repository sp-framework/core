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
 * Table schema definition for Templates (Basepackages Templates).
 */
class Templates
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
                            'size'          => 100,
                            'notNull'       => true
                        ]
                    ),
                    new Column(
                        'component_id',
                        [
                            'type'          => Column::TYPE_SMALLINTEGER,
                            'notNull'       => true
                        ]
                    ),
                    new Column(
                        'description',
                        [
                            'type'          => Column::TYPE_VARCHAR,
                            'size'          => 1024,
                            'notNull'       => false
                        ]
                    ),
                    new Column(
                        'in_use',
                        [
                            'type'          => Column::TYPE_BOOLEAN,
                            'notNull'       => true
                        ]
                    ),
                    new Column(
                        'file_name',
                        [
                            'type'          => Column::TYPE_VARCHAR,
                            'size'          => 512,
                            'notNull'       => true
                        ]
                    ),
                    new Column(
                        'html_code',
                        [
                            'type'          => Column::TYPE_MEDIUMTEXT,
                            'notNull'       => true,
                        ]
                    ),
                ]
            ];
    }
}
