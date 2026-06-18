<?php

namespace System\Base\Installer\Packages\Setup\Schema\Basepackages;

use Phalcon\Db\Column;
use Phalcon\Db\Index;

class ActivityLogs
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
                        'activity_type',
                        [
                            'type'    => Column::TYPE_TINYINTEGER,
                            'notNull' => true
                        ]
                    ),
                    new Column(
                        'account_id',
                        [
                            'type'    => Column::TYPE_INTEGER,
                            'notNull' => true
                        ]
                    ),
                    new Column(
                        'created_at',
                        [
                            'type'    => Column::TYPE_TIMESTAMP,
                            'notNull' => true,
                            'default' => 'CURRENT_TIMESTAMP'
                        ]
                    ),
                    new Column(
                        'package_class',
                        [
                            'type'    => Column::TYPE_VARCHAR,
                            'size'    => 200,
                            'notNull' => true
                        ]
                    ),
                    new Column(//Source Row Id
                        'package_row_id',
                        [
                            'type'    => Column::TYPE_INTEGER,
                            'notNull' => true
                        ]
                    ),
                    new Column(
                        'log',
                        [
                            'type'    => Column::TYPE_MEDIUMTEXT,
                            'notNull' => true,
                        ]
                    ),
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
                    'package_row_id',
                    'package_class',
                    'activity_type',
                    'account_id'
                ],
                'INDEX'
            )
        ];
    }
}