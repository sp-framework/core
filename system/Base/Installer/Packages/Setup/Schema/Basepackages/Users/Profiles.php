<?php

namespace System\Base\Installer\Packages\Setup\Schema\Basepackages\Users;

use Phalcon\Db\Column;
use Phalcon\Db\Index;

class Profiles
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
                        'locale_country_id',
                        [
                            'type'          => Column::TYPE_SMALLINTEGER,
                            'notNull'       => false,
                        ]
                    ),
                    new Column(
                        'locale_timezone_id',
                        [
                            'type'          => Column::TYPE_SMALLINTEGER,
                            'notNull'       => false,
                        ]
                    ),
                    new Column(
                        'settings',
                        [
                            'type'          => Column::TYPE_JSON,
                            'notNull'       => false,
                        ]
                    )
                ],
                'indexes' => [
                    new Index(
                        'column_UNIQUE',
                        [
                            'account_id'
                        ],
                        'UNIQUE'
                    )
                ]
            ];
    }

    public function indexes()
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