<?php

namespace System\Base\Installer\Packages\Setup\Schema\Basepackages;

use Phalcon\Db\Column;

class Notifications
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
                            'type'    => Column::TYPE_VARCHAR,
                            'size'    => 100,
                            'notNull' => false
                        ]
                    ),
                    new Column(//Source Row Id
                        'package_row_id',
                        [
                            'type'    => Column::TYPE_INTEGER,
                            'notNull' => false
                        ]
                    ),
                    new Column(
                        'notification_type',
                        [
                            'type'    => Column::TYPE_TINYINTEGER,
                            'notNull' => true,
                            'default' => '0'
                        ]
                    ),
                    new Column(
                        'app_id',
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
                        'created_by',
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
                        'notification_title',
                        [
                            'type'    => Column::TYPE_VARCHAR,
                            'size'    => 4096,
                            'notNull' => true
                        ]
                    ),
                    new Column(
                        'notification_details',
                        [
                            'type'    => Column::TYPE_TEXT,
                            'notNull' => false
                        ]
                    ),
                    new Column(
                        'read',
                        [
                            'type'    => Column::TYPE_TINYINTEGER,
                            'notNull' => true,
                            'default' => '0'
                        ]
                    ),
                    new Column(
                        'archive',
                        [
                            'type'    => Column::TYPE_TINYINTEGER,
                            'notNull' => true,
                            'default' => '0'
                        ]
                    )
                ]
            ];
    }
}