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
                        'portrait',
                        [
                            'type'          => Column::TYPE_VARCHAR,
                            'size'          => 1024,
                            'notNull'       => false,
                        ]
                    ),
                    new Column(
                        'first_name',
                        [
                            'type'          => Column::TYPE_VARCHAR,
                            'size'          => 50,
                            'notNull'       => true,
                        ]
                    ),
                    new Column(
                        'last_name',
                        [
                            'type'          => Column::TYPE_VARCHAR,
                            'size'          => 50,
                            'notNull'       => true,
                        ]
                    ),
                    new Column(
                        'full_name',
                        [
                            'type'          => Column::TYPE_VARCHAR,
                            'size'          => 100,
                            'notNull'       => true,
                        ]
                    ),
                    new Column(
                        'contact_address_id',
                        [
                            'type'          => Column::TYPE_INTEGER,
                            'notNull'       => false,
                        ]
                    ),
                    new Column(
                        'contact_phone',
                        [
                            'type'          => Column::TYPE_VARCHAR,
                            'size'          => 100,
                            'notNull'       => true,
                        ]
                    ),
                    new Column(
                        'contact_phone_ext',
                        [
                            'type'          => Column::TYPE_VARCHAR,
                            'size'          => 20,
                            'notNull'       => false,
                        ]
                    ),
                    new Column(
                        'contact_mobile',
                        [
                            'type'          => Column::TYPE_VARCHAR,
                            'size'          => 100,
                            'notNull'       => true,
                        ]
                    ),
                    new Column(
                        'contact_fax',
                        [
                            'type'          => Column::TYPE_VARCHAR,
                            'size'          => 100,
                            'notNull'       => false,
                        ]
                    ),
                    new Column(
                        'secondary_email',
                        [
                            'type'          => Column::TYPE_VARCHAR,
                            'size'          => 100,
                            'notNull'       => false,
                        ]
                    ),
                    new Column(
                        'cc_emails_to_secondary_email',
                        [
                            'type'          => Column::TYPE_TINYINTEGER,
                            'notNull'       => false,
                        ]
                    ),
                    new Column(
                        'contact_other',
                        [
                            'type'          => Column::TYPE_VARCHAR,
                            'size'          => 200,
                            'notNull'       => false,
                        ]
                    ),
                    new Column(
                        'contact_notes',
                        [
                            'type'          => Column::TYPE_VARCHAR,
                            'size'          => 2048,
                            'notNull'       => false,
                        ]
                    ),
                    new Column(
                        'settings',
                        [
                            'type'          => Column::TYPE_TEXT,
                            'notNull'       => false,
                        ]
                    )
                ],
                'options' => [
                    'TABLE_COLLATION'       => 'utf8mb4_general_ci'
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
}