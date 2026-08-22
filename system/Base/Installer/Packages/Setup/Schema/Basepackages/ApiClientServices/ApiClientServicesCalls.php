<?php

declare(strict_types=1);

/**
 * SP Framework
 *
 * @package     System\Base\Installer\Packages\Setup\Schema\Basepackages\ApiClientServices
 * @copyright   Copyright (c) 2026
 * @link        https://github.com/sp-framework/core
 */

namespace System\Base\Installer\Packages\Setup\Schema\Basepackages\ApiClientServices;

use Phalcon\Db\Column;

/**
 * Table schema definition for ApiClientServicesCalls (Basepackages Api Client Services Api Client Services Calls).
 */
class ApiClientServicesCalls
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
                    'api_id',
                    [
                        'type'    => Column::TYPE_INTEGER,
                        'notNull' => true,
                    ]
                ),
                new Column(
                    'called_at',
                    [
                        'type'    => Column::TYPE_TIMESTAMP,
                        'notNull' => true,
                        'default' => 'CURRENT_TIMESTAMP',
                    ]
                ),
                new Column(
                    'call_method',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 100,
                        'notNull' => true,
                    ]
                ),
                new Column(
                    'call_response_code',
                    [
                        'type'    => Column::TYPE_SMALLINTEGER,
                        'notNull' => true,
                    ]
                ),
                new Column(
                    'call_exec_time',
                    [
                        'type'    => Column::TYPE_FLOAT,
                        'notNull' => true,
                    ]
                ),
                new Column(
                    'call_stats',
                    [
                        'type'    => Column::TYPE_TEXT,
                        'notNull' => true,
                    ]
                ),
                new Column(
                    'call_error',
                    [
                        'type'    => Column::TYPE_TEXT,
                        'notNull' => false,
                    ]
                ),
            ]
        ];
    }
}
