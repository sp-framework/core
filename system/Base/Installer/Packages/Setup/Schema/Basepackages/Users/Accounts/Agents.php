<?php

namespace System\Base\Installer\Packages\Setup\Schema\Basepackages\Users\Accounts;

use Phalcon\Db\Column;

class Agents
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
                        'account_id',
                        [
                            'type'          => Column::TYPE_INTEGER,
                            'notNull'       => true,
                        ]
                    ),
                    new Column(
                        'session_id',
                        [
                            'type'          => Column::TYPE_VARCHAR,
                            'size'          => 100,
                            'notNull'       => true,
                        ]
                    ),
                    new Column(
                        'client_address',
                        [
                            'type'          => Column::TYPE_VARCHAR,
                            'size'          => 100,
                            'notNull'       => true,
                        ]
                    ),
                    new Column(
                        'user_agent',
                        [
                            'type'          => Column::TYPE_VARCHAR,
                            'size'          => 1024,
                            'notNull'       => true,
                        ]
                    ),
                    new Column(
                        'verified',
                        [
                            'type'          => Column::TYPE_BOOLEAN,
                            'notNull'       => true,
                        ]
                    ),
                    new Column(
                        'verification_code',
                        [
                            'type'              => Column::TYPE_VARCHAR,
                            'size'              => 50,
                            'notNull'           => false,
                        ]
                    ),
                ],
                'options' => [
                    'TABLE_COLLATION' => 'utf8mb4_general_ci'
                ]
            ];
    }
}