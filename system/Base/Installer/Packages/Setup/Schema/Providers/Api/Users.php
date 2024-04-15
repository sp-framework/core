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
                    'account_id',
                    [
                        'type'          => Column::TYPE_INTEGER,
                        'notNull'       => true,
                    ]
                )
            ],
            'options' => [
                'TABLE_COLLATION' => 'utf8mb4_general_ci'
            ]
        ];
    }
}