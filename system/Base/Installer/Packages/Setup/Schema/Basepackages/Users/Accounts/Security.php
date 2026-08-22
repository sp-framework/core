<?php

declare(strict_types=1);

/**
 * SP Framework
 *
 * @package     System\Base\Installer\Packages\Setup\Schema\Basepackages\Users\Accounts
 * @copyright   Copyright (c) 2026
 * @link        https://github.com/sp-framework/core
 */

namespace System\Base\Installer\Packages\Setup\Schema\Basepackages\Users\Accounts;

use Phalcon\Db\Column;
use Phalcon\Db\Index;

/**
 * Table schema definition for Security (Basepackages Users Accounts Security).
 */
class Security
{
    /**
     * Defines table columns and data types.
     *
     * @return array<string, array<int, Column>> Columns definition array.
     */
    public function columns(): array
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
                            'type'          => Column::TYPE_JSON,
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
                        'twofa_otp_status',
                        [
                            'type'          => Column::TYPE_BOOLEAN,
                            'notNull'       => false,
                        ]
                    ),
                    new Column(
                        'twofa_otp_secret',
                        [
                            'type'          => Column::TYPE_VARCHAR,
                            'size'          => 1024,
                            'notNull'       => false
                        ]
                    ),
                    new Column(
                        'twofa_otp_hotp_counter',
                        [
                            'type'          => Column::TYPE_INTEGER,
                            'notNull'       => false
                        ]
                    ),
                    new Column(
                        'twofa_email_code',
                        [
                            'type'          => Column::TYPE_VARCHAR,
                            'size'          => 1024,
                            'notNull'       => false
                        ]
                    ),
                    new Column(
                        'twofa_email_code_sent_on',
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
                    ),
                    new Column(
                        'forgotten_request',
                        [
                            'type'          => Column::TYPE_BOOLEAN,
                            'notNull'       => false
                        ]
                    ),
                    new Column(
                        'forgotten_request_session_id',
                        [
                            'type'          => Column::TYPE_VARCHAR,
                            'size'          => 50,
                            'notNull'       => false,
                        ]
                    ),
                    new Column(
                        'forgotten_request_ip',
                        [
                            'type'          => Column::TYPE_VARCHAR,
                            'size'          => 100,
                            'notNull'       => false,
                        ]
                    ),
                    new Column(
                        'forgotten_request_agent',
                        [
                            'type'          => Column::TYPE_VARCHAR,
                            'size'          => 2048,
                            'notNull'       => false,
                        ]
                    ),
                    new Column(
                        'forgotten_request_code',
                        [
                            'type'          => Column::TYPE_VARCHAR,
                            'size'          => 2048,
                            'notNull'       => false,
                        ]
                    ),
                    new Column(
                        'forgotten_request_sent_on',
                        [
                            'type'          => Column::TYPE_INTEGER,
                            'notNull'       => false
                        ]
                    )
                ]
            ];
    }

    /**
     * Defines table indexes and database constraints.
     *
     * @return array<int, Index> Indexes definition array.
     */
    public function indexes(): array
    {
        return
        [
            new Index(
                'column_INDEX',
                [
                    'account_id'
                ],
                'INDEX'
            )
        ];
    }
}
