<?php

declare(strict_types=1);

/**
 * SP Framework
 *
 * @package     System\Base\Installer\Packages\Setup\Schema\Basepackages\Dashboards
 * @copyright   Copyright (c) 2026
 * @link        https://github.com/sp-framework/core
 */

namespace System\Base\Installer\Packages\Setup\Schema\Basepackages\Dashboards;

use Phalcon\Db\Column;

/**
 * Table schema definition for Widgets (Basepackages Dashboards Widgets).
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
                    'widget_id',
                    [
                        'type'          => Column::TYPE_INTEGER,
                        'notNull'       => true
                    ]
                ),
                new Column(
                    'dashboard_id',
                    [
                        'type'          => Column::TYPE_SMALLINTEGER,
                        'notNull'       => true
                    ]
                ),
                new Column(
                    'sequence',
                    [
                        'type'    => Column::TYPE_TINYINTEGER,
                        'notNull' => true
                    ]
                ),
                new Column(
                    'settings',
                    [
                        'type'    => Column::TYPE_JSON,
                        'notNull' => true
                    ]
                ),
            ]
        ];
    }
}
