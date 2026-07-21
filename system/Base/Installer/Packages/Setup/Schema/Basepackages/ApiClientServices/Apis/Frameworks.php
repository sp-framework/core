<?php

namespace System\Base\Installer\Packages\Setup\Schema\Basepackages\ApiClientServices\Apis;

use Phalcon\Db\Column;
use Phalcon\Db\Index;

class Frameworks
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
                        'api_url',
                        [
                            'type'    => Column::TYPE_VARCHAR,
                            'size'    => 100,
                            'notNull' => true,
                        ]
                    ),
                    new Column(
                        'auth_type',
                        [
                            'type'    => Column::TYPE_VARCHAR,
                            'size'    => 50,
                            'notNull' => true,
                        ]
                    ),
                    new Column(
                        'username',
                        [
                            'type'    => Column::TYPE_VARCHAR,
                            'size'    => 100,
                            'notNull' => false,
                        ]
                    ),
                    new Column(
                        'password',
                        [
                            'type'    => Column::TYPE_VARCHAR,
                            'size'    => 512,
                            'notNull' => false,
                        ]
                    ),
                    new Column(
                        'authorization',
                        [
                            'type'    => Column::TYPE_VARCHAR,
                            'size'    => 512,
                            'notNull' => false,
                        ]
                    ),
                    new Column(
                        'device_id',
                        [
                            'type'          => Column::TYPE_VARCHAR,
                            'size'          => 100,
                            'notNull'       => false,
                        ]
                    ),
                    new Column(
                        'client_id',
                        [
                            'type'          => Column::TYPE_VARCHAR,
                            'size'          => 100,
                            'notNull'       => false
                        ]
                    ),
                    new Column(
                        'client_secret',
                        [
                            'type'          => Column::TYPE_VARCHAR,
                            'size'          => 1024,
                            'notNull'       => false,
                        ]
                    ),
                    new Column(
                        'code',
                        [
                            'type'          => Column::TYPE_VARCHAR,
                            'size'          => 100,
                            'notNull'       => false,
                        ]
                    ),
                    new Column(
                        'request_url',
                        [
                            'type'          => Column::TYPE_VARCHAR,
                            'size'          => 2048,
                            'notNull'       => false,
                        ]
                    ),
                    new Column(
                        'redirect_uri',
                        [
                            'type'          => Column::TYPE_VARCHAR,
                            'size'          => 2048,
                            'notNull'       => false,
                        ]
                    ),
                    new Column(
                        'refresh_url',
                        [
                            'type'          => Column::TYPE_VARCHAR,
                            'size'          => 2048,
                            'notNull'       => false,
                        ]
                    ),
                    new Column(
                        'access_token',
                        [
                            'type'          => Column::TYPE_VARCHAR,
                            'size'          => 2048,
                            'notNull'       => false,
                        ]
                    ),
                    new Column(
                        'refresh_token',
                        [
                            'type'          => Column::TYPE_VARCHAR,
                            'size'          => 2048,
                            'notNull'       => false,
                        ]
                    ),
                    new Column(//password, client_credentials, authorization_code
                        'grant_type',
                        [
                            'type'          => Column::TYPE_VARCHAR,
                            'size'          => 50,
                            'notNull'       => false,
                        ]
                    ),
                    new Column(
                        'token_type',
                        [
                            'type'          => Column::TYPE_VARCHAR,
                            'size'          => 20,
                            'notNull'       => false,
                        ]
                    ),
                    new Column(
                        'expires',
                        [
                            'type'          => Column::TYPE_TIMESTAMP,
                            'notNull'       => false,
                        ]
                    ),
                ],
                'indexes' => [
                    new Index(
                        'column_UNIQUE',
                        [
                            'api_url',
                            'auth_type',
                            'username',
                            'authorization',
                            'client_id'
                        ],
                        'UNIQUE'
                    )
                ]
            ];
    }
}