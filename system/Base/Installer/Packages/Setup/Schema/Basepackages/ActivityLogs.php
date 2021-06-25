<?php

namespace System\Base\Installer\Packages\Setup\Schema\Basepackages;

use Phalcon\Db\Column;

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
                            'type'          => Column::TYPE_TINYINTEGER,
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
                        'package_name',
                        [
                            'type'    => Column::TYPE_VARCHAR,
                            'size'    => 100,
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
                        'logs',
                        [
                            'type'    => Column::TYPE_MEDIUMTEXT,
                            'notNull' => true,
                        ]
                    ),
                ]
            ];
    }
}