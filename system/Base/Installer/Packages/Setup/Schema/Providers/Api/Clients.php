<?php

namespace System\Base\Installer\Packages\Setup\Schema\Providers\Api;

use Phalcon\Db\Column;

class Clients
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
                    'api_id',
                    [
                        'type'          => Column::TYPE_TINYINTEGER,
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
                    'email',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 100,
                        'notNull'       => true,
                    ]
                ),
                new Column(
                    'device_id',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 512,
                        'notNull'       => false,
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
                    'client_secret',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 1024,
                        'notNull'       => true,
                    ]
                ),
                new Column(//As per Oauth2 Naming schema
                    'name',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 1024,
                        'notNull'       => true,
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
                new Column(//For monitoring Activity
                    'last_used',
                    [
                        'type'          => Column::TYPE_TIMESTAMP,
                        'notNull'       => true
                    ]
                ),
                new Column(
                    'revoked',
                    [
                        'type'          => Column::TYPE_BOOLEAN,
                        'notNull'       => true
                    ]
                ),
                new Column(
                    'concurrent_calls_count',
                    [
                        'type'          => Column::TYPE_INTEGER,
                        'notNull'       => false
                    ]
                ),
                // new Column(
                //     'concurrent_calls_connections',
                //     [
                //         'type'          => Column::TYPE_VARCHAR,
                //         'size'          => 2048,
                //         'notNull'       => false
                //     ]
                // ),
                new Column(
                    'per_minute_calls_count',
                    [
                        'type'          => Column::TYPE_INTEGER,
                        'notNull'       => false
                    ]
                ),
                new Column(
                    'per_minute_calls_start',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 50,
                        'notNull'       => false
                    ]
                ),
                // new Column(
                //     'per_minute_calls_connections',
                //     [
                //         'type'          => Column::TYPE_VARCHAR,
                //         'size'          => 2048,
                //         'notNull'       => false
                //     ]
                // ),
                new Column(
                    'per_hour_calls_count',
                    [
                        'type'          => Column::TYPE_INTEGER,
                        'notNull'       => false
                    ]
                ),
                new Column(
                    'per_hour_calls_start',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 50,
                        'notNull'       => false
                    ]
                ),
                // new Column(
                //     'per_hour_calls_connections',
                //     [
                //         'type'          => Column::TYPE_VARCHAR,
                //         'size'          => 2048,
                //         'notNull'       => false
                //     ]
                // ),
                new Column(
                    'per_day_calls_count',
                    [
                        'type'          => Column::TYPE_INTEGER,
                        'notNull'       => false
                    ]
                ),
                new Column(
                    'per_day_calls_start',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 50,
                        'notNull'       => false
                    ]
                ),
                // new Column(
                //     'per_day_calls_connections',
                //     [
                //         'type'          => Column::TYPE_VARCHAR,
                //         'size'          => 2048,
                //         'notNull'       => false
                //     ]
                // ),
            ],
            'options' => [
                'TABLE_COLLATION' => 'utf8mb4_general_ci'
            ]
        ];
    }
}