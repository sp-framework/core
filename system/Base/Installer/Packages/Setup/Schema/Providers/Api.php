<?php

namespace System\Base\Installer\Packages\Setup\Schema\Providers;

use Phalcon\Db\Column;
use Phalcon\Db\Index;

class Api
{
    public function columns()
    {
        return
        [
           'columns' => [
                new Column(
                    'id',
                    [
                        'type'          => Column::TYPE_SMALLINTEGER,
                        'notNull'       => true,
                        'autoIncrement' => true,
                        'primary'       => true,
                    ]
                ),
                new Column(
                    'status',
                    [
                        'type'          => Column::TYPE_BOOLEAN,
                        'notNull'       => true,
                    ]
                ),
                new Column(
                    'is_public',
                    [
                        'type'          => Column::TYPE_BOOLEAN,
                        'notNull'       => true,
                    ]
                ),
                new Column(
                    'client_keys_generation_allowed',
                    [
                        'type'          => Column::TYPE_BOOLEAN,
                        'notNull'       => false,
                    ]
                ),
                new Column(
                    'registration_allowed',
                    [
                        'type'          => Column::TYPE_BOOLEAN,
                        'notNull'       => false,
                    ]
                ),
                new Column(
                    'name',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 50,
                        'notNull'       => true,
                    ]
                ),
                new Column(
                    'app_id',
                    [
                        'type'          => Column::TYPE_TINYINTEGER,
                        'notNull'       => true,
                    ]
                ),
                new Column(
                    'domain_id',
                    [
                        'type'          => Column::TYPE_TINYINTEGER,
                        'notNull'       => true,
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
                    'description',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 2048,
                        'notNull'       => false,
                    ]
                ),
                new Column(
                    'client_id_length',
                    [
                        'type'          => Column::TYPE_TINYINTEGER,
                        'notNull'       => true,
                    ]
                ),
                new Column(
                    'client_secret_length',
                    [
                        'type'          => Column::TYPE_TINYINTEGER,
                        'notNull'       => true,
                    ]
                ),
                new Column(
                    'grant_type',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 50,
                        'notNull'       => true,
                    ]
                ),
                new Column(
                    'private_key',
                    [
                        'type'          => Column::TYPE_BOOLEAN,
                        'notNull'       => true,
                    ]
                ),
                new Column(
                    'encryption_key_size',
                    [
                        'type'          => Column::TYPE_TINYINTEGER,
                        'notNull'       => true,
                    ]
                ),
                new Column(
                    'pki_key_size',
                    [
                        'type'          => Column::TYPE_TINYINTEGER,
                        'notNull'       => true,
                    ]
                ),
                new Column(
                    'pki_algorithm',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 1024,
                        'notNull'       => true,
                    ]
                ),
                new Column(
                    'private_key_passphrase',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 1024,
                        'notNull'       => true,
                    ]
                ),
                new Column(
                    'private_key_location',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 1024,
                        'notNull'       => true,
                    ]
                ),
                new Column(
                    'access_token_timeout',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 50,
                        'notNull'       => true,
                    ]
                ),
                new Column(
                    'refresh_token_timeout',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 50,
                        'notNull'       => true,
                    ]
                ),
                new Column(
                    'scope_id',
                    [
                        'type'          => Column::TYPE_SMALLINTEGER,
                        'notNull'       => true,
                    ]
                ),
             ],
            'indexes' => [
                new Index(
                    'column_UNIQUE',
                    [
                        'app_id',
                        'domain_id',
                        'grant_type',
                        'scope_id'
                    ],
                    'UNIQUE'
                )
            ],
            'options' => [
                'TABLE_COLLATION' => 'utf8mb4_general_ci'
            ]
        ];
    }
}