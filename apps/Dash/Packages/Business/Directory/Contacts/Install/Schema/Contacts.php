<?php

namespace Apps\Dash\Packages\Business\Directory\Contacts\Install\Schema;

use Phalcon\Db\Column;
use Phalcon\Db\Index;

class Contacts
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
                    'portrait',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 1024,
                        'notNull' => false,
                    ]
                ),
                new Column(
                    'account_id',
                    [
                        'type'    => Column::TYPE_INTEGER,
                        'notNull' => false,
                    ]
                ),
                new Column(
                    'account_email',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 50,
                        'notNull' => true,
                    ]
                ),
                new Column(
                    'vendor_id',
                    [
                        'type'    => Column::TYPE_INTEGER,
                        'notNull' => false,
                    ]
                ),
                new Column(
                    'first_name',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 100,
                        'notNull' => true,
                    ]
                ),
                new Column(
                    'last_name',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 100,
                        'notNull' => true,
                    ]
                ),
                new Column(
                    'full_name',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 200,
                        'notNull' => true,
                    ]
                ),
                new Column(
                    'job_title',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 100,
                        'notNull' => false,
                    ]
                ),
                new Column(
                    'contact_manager_id',
                    [
                        'type'    => Column::TYPE_INTEGER,
                        'notNull' => false,
                    ]
                ),
                new Column(
                    'contact_source',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 50,
                        'notNull' => false,
                    ]
                ),
                new Column(
                    'contact_source_details',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 100,
                        'notNull' => false,
                    ]
                ),
                new Column(
                    'contact_referrer_id',
                    [
                        'type'    => Column::TYPE_INTEGER,
                        'notNull' => false,
                    ]
                ),
                new Column(
                    'contact_phone',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 100,
                        'notNull' => true,
                    ]
                ),
                new Column(
                    'contact_phone_ext',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 20,
                        'notNull' => false,
                    ]
                ),
                new Column(
                    'contact_mobile',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 100,
                        'notNull' => true,
                    ]
                ),
                new Column(
                    'contact_fax',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 100,
                        'notNull' => false,
                    ]
                ),
                new Column(
                    'contact_secondary_email',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 100,
                        'notNull' => false,
                    ]
                ),
                new Column(
                    'cc_emails_to_secondary_email',
                    [
                        'type'    => Column::TYPE_TINYINTEGER,
                        'notNull' => false,
                    ]
                ),
                new Column(
                    'contact_other',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 200,
                        'notNull' => false,
                    ]
                ),
                new Column(
                    'address_ids',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 100,
                        'notNull' => false,
                    ]
                ),
                new Column(//Encrypted text field with CC information of the customer. The key is with customer and cannot be viewed even if the data is stolen.
                    'cc_details',
                    [
                        'type'          => Column::TYPE_TEXT,
                        'notNull'       => false,
                    ]
                ),
            ],
            'options' => [
                'TABLE_COLLATION' => 'utf8mb4_general_ci'
            ],
            'indexes' => [
                new Index(
                    'column_UNIQUE',
                    [
                        'account_email'
                    ],
                    'UNIQUE'
                )
            ]
        ];
    }
}