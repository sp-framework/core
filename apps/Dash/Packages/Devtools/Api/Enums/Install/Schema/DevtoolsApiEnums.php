<?php

namespace Apps\Dash\Packages\Devtools\Api\Enums\Install\Schema;

use Phalcon\Db\Column;

class DevtoolsApiEnums
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
                    'name',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 50,
                        'notNull' => true,
                    ]
                ),
                new Column(
                    'contract_id',
                    [
                        'type'    => Column::TYPE_INTEGER,
                        'notNull' => true,
                    ]
                ),
                new Column(
                    'constant_prefix',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 50,
                        'notNull' => false,
                    ]
                ),
                new Column(
                    'enums',
                    [
                        'type'    => Column::TYPE_TEXT,
                        'notNull' => true,
                    ]
                )
            ],
            'options' => [
                'TABLE_COLLATION' => 'utf8mb4_general_ci'
            ]
        ];
    }
}