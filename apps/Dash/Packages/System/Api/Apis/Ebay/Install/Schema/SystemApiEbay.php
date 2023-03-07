<?php

namespace Apps\Dash\Packages\System\Api\Apis\Ebay\Install\Schema;

use Phalcon\Db\Column;

class SystemApiEbay
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
                    'marketplace_id',
                    [
                        'type'    => Column::TYPE_CHAR,
                        'size'    => 50,
                        'notNull' => true,
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
                    'user_credentials_app_id',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 512,
                        'notNull' => false,
                    ]
                ),
                new Column(
                    'user_credentials_dev_id',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 512,
                        'notNull' => false,
                    ]
                ),
                new Column(
                    'user_credentials_cert_id',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 512,
                        'notNull' => false,
                    ]
                ),
                new Column(
                    'user_credentials_ru_name',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 512,
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
                    'app_access_token',
                    [
                        'type'    => Column::TYPE_TEXT,
                        'notNull' => false,
                    ]
                ),
                new Column(
                    'app_access_token_valid_until',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 50,
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