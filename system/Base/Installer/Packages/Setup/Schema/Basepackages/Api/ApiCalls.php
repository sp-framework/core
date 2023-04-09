<?php

namespace System\Base\Installer\Packages\Setup\Schema\Basepackages\Api;

use Phalcon\Db\Column;

class ApiCalls
{
    public function columns()
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
                        'type'    => Column::TYPE_TINYINTEGER,
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
                        'size'    => 50,
                        'notNull' => true,
                    ]
                ),
                new Column(
                    'call_response_code',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 5,
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
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 4096,
                        'notNull' => false,
                    ]
                ),
            ],
            'options' => [
                'TABLE_COLLATION' => 'utf8mb4_general_ci'
            ],
        ];
    }
}