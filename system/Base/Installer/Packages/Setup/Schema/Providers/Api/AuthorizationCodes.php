<?php

namespace System\Base\Installer\Packages\Setup\Schema\Providers\Api;

use Phalcon\Db\Column;

class AuthorizationCodes
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
                    'api_id',
                    [
                        'type'          => Column::TYPE_INTEGER,
                        'notNull'       => true,
                    ]
                ),
                new Column(
                    'app_id',
                    [
                        'type'          => Column::TYPE_INTEGER,
                        'notNull'       => true,
                    ]
                ),
                new Column(
                    'domain_id',
                    [
                        'type'          => Column::TYPE_INTEGER,
                        'notNull'       => true,
                    ]
                ),
                new Column(
                    'client_id',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 512,
                        'notNull'       => true
                    ]
                ),
                new Column(
                    'account_id',
                    [
                        'type'          => Column::TYPE_INTEGER,
                        'notNull'       => true
                    ]
                ),
                new Column(
                    'authorization_code',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 1024,
                        'notNull'       => true,
                    ]
                ),
                new Column(
                    'expires',
                    [
                        'type'          => Column::TYPE_TIMESTAMP,
                        'notNull'       => true
                    ]
                ),
                new Column(//As per Oauth2 Naming schema
                    'redirectUri',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 2048,
                        'notNull'       => true,
                    ]
                ),
                new Column(
                    'revoked',
                    [
                        'type'          => Column::TYPE_BOOLEAN,
                        'notNull'       => false
                    ]
                ),
            ],
            'options' => [
                'TABLE_COLLATION' => 'utf8mb4_general_ci'
            ]
        ];
    }
}