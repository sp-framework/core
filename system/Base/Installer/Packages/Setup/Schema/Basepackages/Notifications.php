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
                            'type'          => Column::TYPE_TINYINTEGER,
                            'notNull'       => true,
                            'autoIncrement' => true,
                            'primary'       => true,
                        ]
                    ),
                    new Column(
                        'notification_type',
                        [
                            'type'    => Column::TYPE_TINYINTEGER,
                            'notNull' => true
                        ]
                    ),
                    new Column(
                        'notification',
                        [
                            'type'    => Column::TYPE_TEXT,
                            'notNull' => true
                        ]
                    ),
                    new Column(
                        'account_id',
                        [
                            'type'    => Column::TYPE_INTEGER,
                            'notNull' => true,
                            'default' => '0'
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
                    ),
                    new Column(
                        'created_at',
                        [
                            'type'    => Column::TYPE_TIMESTAMP,
                            'notNull' => true,
                            'default' => 'CURRENT_TIMESTAMP'
                        ]
                    )
                ]
            ];
    }
}