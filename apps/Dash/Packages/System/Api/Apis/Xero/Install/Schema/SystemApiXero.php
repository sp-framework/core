<?php

namespace Apps\Dash\Packages\System\Api\Apis\Xero\Install\Schema;

use Phalcon\Db\Column;

class SystemApiXero
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
                    'tenant_id',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 512,
                        'notNull' => false,
                    ]
                ),
                new Column(
                    'tenant_type',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 100,
                        'notNull' => false,
                    ]
                ),
                new Column(
                    'tenant_name',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 100,
                        'notNull' => false,
                    ]
                ),
                new Column(
                    'tenants',
                    [
                        'type'    => Column::TYPE_TEXT,
                        'notNull' => false,
                    ]
                ),
                new Column(
                    'auth_event_id',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 512,
                        'notNull' => false,
                    ]
                ),
                new Column(
                    'use_systems_credentials',
                    [
                        'type'    => Column::TYPE_TINYINTEGER,
                        'notNull' => true,
                    ]
                ),
                new Column(
                    'user_credentials_client_id',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 512,
                        'notNull' => false,
                    ]
                ),
                new Column(
                    'user_credentials_client_secret',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 512,
                        'notNull' => false,
                    ]
                ),
                new Column(
                    'user_credentials_redirect_uri',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 1024,
                        'notNull' => false,
                    ]
                ),
                new Column(
                    'user_credentials_scopes',
                    [
                        'type'    => Column::TYPE_TEXT,
                        'notNull' => false,
                    ]
                ),
                new Column(
                    'user_id_token',
                    [
                        'type'    => Column::TYPE_TEXT,
                        'notNull' => false,
                    ]
                ),
                new Column(
                    'identifier',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 1024,
                        'notNull' => false,
                    ]
                ),
                new Column(
                    'user_access_token',
                    [
                        'type'    => Column::TYPE_TEXT,
                        'notNull' => false,
                    ]
                ),
                new Column(
                    'user_access_token_valid_until',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 50,
                        'notNull' => false,
                    ]
                ),
                new Column(
                    'refresh_token',
                    [
                        'type'    => Column::TYPE_TEXT,
                        'notNull' => false,
                    ]
                ),
                new Column(
                    'refresh_token_valid_until',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 50,
                        'notNull' => false,
                    ]
                )
            ],
            'options'   => [
                'TABLE_COLLATION' => 'utf8mb4_general_ci'
            ]
        ];
    }
}