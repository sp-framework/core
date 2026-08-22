<?php

declare(strict_types=1);

/**
 * SP Framework
 *
 * @package     System\Base\Installer\Packages\Setup\Schema\Modules\Views
 * @copyright   Copyright (c) 2026
 * @link        https://github.com/sp-framework/core
 */

namespace System\Base\Installer\Packages\Setup\Schema\Modules\Views;

use Phalcon\Db\Column;

/**
 * Table schema definition for Settings (Modules Views Settings).
 */
class Settings
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
                            'type'          => Column::TYPE_SMALLINTEGER,
                            'notNull'       => true,
                            'autoIncrement' => true,
                            'primary'       => true,
                        ]
                    ),
                    new Column(
                        'view_id',
                        [
                            'type'    => Column::TYPE_SMALLINTEGER,
                            'notNull' => true,
                        ]
                    ),
                    new Column(
                        'domain_id',
                        [
                            'type'    => Column::TYPE_INTEGER,
                            'notNull' => true,
                        ]
                    ),
                    new Column(
                        'app_id',
                        [
                            'type'    => Column::TYPE_INTEGER,
                            'notNull' => true,
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
