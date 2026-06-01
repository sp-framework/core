<?php

namespace System\Base\Installer\Packages\Setup\Schema\Basepackages;

use Phalcon\Db\Column;
use Phalcon\Db\Index;

class Mutex
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
                        'package_name',
                        [
                            'type'          => Column::TYPE_VARCHAR,
                            'size'          => 100,
                            'notNull'       => true
                        ]
                    ),
                    new Column(
                        'package_row_id',
                        [
                            'type'          => Column::TYPE_INTEGER,
                            'notNull'       => true
                        ]
                    ),
                    new Column(
                        'parent_lock_id',
                        [
                            'type'          => Column::TYPE_INTEGER,
                            'notNull'       => true,
                        ]
                    ),
                    new Column(
                        'account_id',
                        [
                            'type'          => Column::TYPE_INTEGER,
                            'notNull'       => true,
                        ]
                    ),
                    new Column(
                        'locked_at',
                        [
                            'type'          => Column::TYPE_INTEGER,
                            'notNull'       => true,
                        ]
                    ),
                ],
                'indexes' => [
                    new Index(
                        'column_UNIQUE',
                        [
                            'package_name',
                            'package_row_id',
                            'account_id'
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
                    'package_name',
                    'package_row_id',
                    'account_id',
                    'parent_lock_id'
                ],
                'INDEX'
            )
        ];
    }
}