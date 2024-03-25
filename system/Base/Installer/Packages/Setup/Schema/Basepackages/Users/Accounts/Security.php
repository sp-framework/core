<?php

namespace System\Base\Installer\Packages\Setup\Schema\Basepackages\Users\Accounts;

use Phalcon\Db\Column;

class Security
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
                        'password',
                        [
                            'type'          => Column::TYPE_VARCHAR,
                            'size'          => 2048,
                            'notNull'       => true,
                        ]
                    ),
                    new Column(
                        'role_id',
                        [
                            'type'          => Column::TYPE_SMALLINTEGER,
                            'notNull'       => true,
                        ]
                    ),
                    new Column(
                        'override_role',
                        [
                            'type'          => Column::TYPE_BOOLEAN,
                            'notNull'       => true,
                        ]
                    ),
                    new Column(
                        'permissions',
                        [
                            'type'          => Column::TYPE_TEXT,
                            'notNull'       => true,
                        ]
                    ),
                    new Column(
                        'force_pwreset',
                        [
                            'type'          => Column::TYPE_BOOLEAN,
                            'notNull'       => false,
                        ]
                    ),
                    new Column(
                        'two_fa_totp_status',
                        [
                            'type'          => Column::TYPE_BOOLEAN,
                            'notNull'       => false,
                        ]
                    ),
                    new Column(
                        'two_fa_totp_secret',
                        [
                            'type'          => Column::TYPE_VARCHAR,
                            'size'          => 1024,
                            'notNull'       => false
                        ]
                    ),
                    new Column(
                        'two_fa_email_code',
                        [
                            'type'          => Column::TYPE_VARCHAR,
                            'size'          => 1024,
                            'notNull'       => false
                        ]
                    ),
                    new Column(
                        'two_fa_email_code_sent_on',
                        [
                            'type'          => Column::TYPE_INTEGER,
                            'notNull'       => false
                        ]
                    ),
                    new Column(
                        'password_history',
                        [
                            'type'          => Column::TYPE_JSON,
                            'notNull'       => false
                        ]
                    ),
                    new Column(
                        'password_set_on',
                        [
                            'type'          => Column::TYPE_INTEGER,
                            'notNull'       => true
                        ]
                    ),
                    new Column(
                        'force_pwreset_after',
                        [
                            'type'          => Column::TYPE_INTEGER,
                            'notNull'       => false
                        ]
                    )
                ],
                'options' => [
                    'TABLE_COLLATION' => 'utf8mb4_general_ci'
                ]
            ];
    }
}