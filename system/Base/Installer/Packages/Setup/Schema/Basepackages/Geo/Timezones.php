<?php

declare(strict_types=1);

/**
 * SP Framework
 *
 * @package     System\Base\Installer\Packages\Setup\Schema\Basepackages\Geo
 * @copyright   Copyright (c) 2026
 * @link        https://github.com/sp-framework/core
 */

namespace System\Base\Installer\Packages\Setup\Schema\Basepackages\Geo;

use Phalcon\Db\Column;

/**
 * Table schema definition for Timezones (Basepackages Geo Timezones).
 */
class Timezones
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
                    'zone_name',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 512,
                        'notNull'       => false
                    ]
                ),
                new Column(
                    'tz_name',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 255,
                        'notNull'       => false
                    ]
                ),
                new Column(
                    'gmt_offset',
                    [
                        'type'          => Column::TYPE_INTEGER,
                        'notNull'       => false
                    ]
                ),
                new Column(
                    'gmt_offset_name',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 255,
                        'notNull'       => false
                    ]
                ),
                new Column(
                    'abbreviation',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 10,
                        'notNull'       => false
                    ]
                ),
                new Column(
                    'gmt_offset_dst',
                    [
                        'type'          => Column::TYPE_INTEGER,
                        'notNull'       => false
                    ]
                ),
                new Column(
                    'gmt_offset_name_dst',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 255,
                        'notNull'       => false
                    ]
                ),
                new Column(
                    'abbreviation_dst',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 10,
                        'notNull'       => false
                    ]
                )
            ]
        ];
    }
}
