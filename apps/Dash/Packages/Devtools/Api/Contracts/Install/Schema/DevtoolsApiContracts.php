<?php

namespace Apps\Dash\Packages\Devtools\Api\Contracts\Install\Schema;

use Phalcon\Db\Column;

class DevtoolsApiContracts
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
                    'api_type',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 50,
                        'notNull' => true,
                    ]
                ),
                new Column(
                    'link',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 1024,
                        'notNull' => true,
                    ]
                ),
                new Column(
                    'filename',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 100,
                        'notNull' => true,
                    ]
                ),
                new Column(
                    'wsdl_convert',
                    [
                        'type'    => Column::TYPE_TINYINTEGER,
                        'notNull' => false,
                    ]
                ),
                new Column(
                    'content',
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