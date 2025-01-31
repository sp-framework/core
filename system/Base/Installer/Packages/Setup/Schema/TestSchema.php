<?php

namespace System\Base\Installer\Packages\Setup\Schema;

use Phalcon\Db\Column;
use Phalcon\Db\Index;

class TestSchema
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
                        'key',
                        [
                            'type'    => Column::TYPE_VARCHAR,
                            'size'    => 100,
                            'notNull' => true,
                        ]
                    ),
                    new Column(
                        'key2',
                        [
                            'type'    => Column::TYPE_VARCHAR,
                            'size'    => 100,
                            'notNull' => true,
                        ]
                    ),
                    new Column(
                        'query',
                        [
                            'type'    => Column::TYPE_JSON,
                            'notNull' => true,
                        ]
                    ),
                    new Column(
                        'status',
                        [
                            'type'    => Column::TYPE_TINYINTEGER,
                            'size'    => 1,
                            'notNull' => false,
                        ]
                    ),
                    new Column(
                        'bool',
                        [
                            'type'    => Column::TYPE_BOOLEAN,
                            'notNull' => false,
                        ]
                    )
                ],
                'indexes' => [
                    new Index(
                        'column_UNIQUE',
                        [
                            'key'
                        ],
                        'UNIQUE'
                    )
                ]
            ];
    }

    public function indexes()
    {
        return
        [
            new Index(
                'column_INDEX',
                [
                    'key2'
                ],
                'INDEX'
            )
        ];
    }
}