<?php

namespace System\Base\Installer\Packages\Setup\Schema\Providers\Api;

use Phalcon\Db\Column;

class Users
{
    public function columns()
    {
        return
        [
           'columns' => [
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
                    'scope',
                    [
                        'type'          => Column::TYPE_TEXT,
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