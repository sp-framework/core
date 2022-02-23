<?php

namespace Apps\Dash\Packages\System\Messenger\Install\Schema;

use Phalcon\Db\Column;

class SystemMessenger
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
                    'from_account_id',
                    [
                        'type'    => Column::TYPE_INTEGER,
                        'notNull' => true,
                    ]
                ),
                new Column(
                    'to_account_id',
                    [
                        'type'    => Column::TYPE_INTEGER,
                        'notNull' => true,
                    ]
                ),
                new Column(
                    'message',
                    [
                        'type'    => Column::TYPE_TEXT,
                        'notNull' => true,
                    ]
                ),
                new Column(
                    'read',
                    [
                        'type'    => Column::TYPE_TINYINTEGER,
                        'notNull' => false
                    ]
                ),
                new Column(
                    'edited',
                    [
                        'type'    => Column::TYPE_TINYINTEGER,
                        'notNull' => false
                    ]
                ),
                new Column(
                    'removed',
                    [
                        'type'    => Column::TYPE_TINYINTEGER,
                        'notNull' => false
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
                    'updated_at',
                    [
                        'type'    => Column::TYPE_TIMESTAMP,
                        'notNull' => true,
                        'default' => 'CURRENT_TIMESTAMP'
                    ]
                ),
            ],
            'options' => [
                'TABLE_COLLATION' => 'utf8mb4_unicode_ci'
            ]
        ];
    }
}