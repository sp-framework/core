<?php

namespace Apps\Dash\Packages\Crms\Customers\Install\Schema;

use Phalcon\Db\Column;
use Phalcon\Db\Index;

class CrmsCustomers
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
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 1024,
                        'notNull'       => false,
                        'comment'       => 'Portrait (UUID)'
                    ]
                ),
                new Column(
                    'account_id',
                    [
                        'type'          => Column::TYPE_INTEGER,
                        'notNull'       => false,
                        'comment'       => 'Account ID'
                    ]
                ),
                new Column(
                    'account_email',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 50,
                        'notNull'       => false,
                        'comment'       => 'Email'
                    ]
                ),
                new Column(
                    'customer_group_id',
                    [
                        'type'          => Column::TYPE_INTEGER,
                        'notNull'       => false,
                        'comment'       => 'Group ID'
                    ]
                ),
                new Column(
                    'first_name',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 100,
                        'notNull'       => true,
                        'comment'       => 'First Name'
                    ]
                ),
                new Column(
                    'last_name',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 100,
                        'notNull'       => true,
                        'comment'       => 'Last Name'
                    ]
                ),
                new Column(
                    'full_name',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 200,
                        'notNull'       => true,
                        'comment'       => 'Full Name. Concat of First & Last Name'
                    ]
                ),
                new Column(
                    'contact_source',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 50,
                        'notNull'       => false,
                        'comment'       => 'Contact Source'
                    ]
                ),
                new Column(
                    'contact_source_details',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 100,
                        'notNull'       => false,
                        'comment'       => 'Contact Source details'
                    ]
                ),
                new Column(
                    'contact_referrer_id',
                    [
                        'type'          => Column::TYPE_INTEGER,
                        'notNull'       => false,
                        'comment'       => 'Referrer customer ID'
                    ]
                ),
                new Column(
                    'contact_phone',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 100,
                        'notNull'       => false,
                        'comment'       => 'Phone'
                    ]
                ),
                new Column(
                    'contact_phone_ext',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 20,
                        'notNull'       => false,
                        'comment'       => 'Phone Ext'
                    ]
                ),
                new Column(
                    'contact_mobile',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 100,
                        'notNull'       => false,
                        'comment'       => 'Mobile'
                    ]
                ),
                new Column(
                    'contact_secondary_email',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 100,
                        'notNull'       => false,
                        'comment'       => 'Secondary email'
                    ]
                ),
                new Column(
                    'cc_emails_to_secondary_email',
                    [
                        'type'          => Column::TYPE_TINYINTEGER,
                        'notNull'       => false,
                        'comment'       => 'CC to secondary email'
                    ]
                ),
                new Column(
                    'contact_other',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 200,
                        'notNull'       => false,
                        'comment'       => 'Other contact'
                    ]
                ),
                new Column(
                    'address_ids',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 100,
                        'notNull'       => false,
                        'comment'       => 'Addresses IDs'
                    ]
                ),
            ],
            'options' => [
                'TABLE_COLLATION' => 'utf8mb4_general_ci'
            ]
        ];
    }

    public function indexes()
    {
        return
        [
            new Index(
                'column_first_name_index',
                [
                    'first_name'
                ],
                'INDEX'
            ),
            new Index(
                'column_last_name_index',
                [
                    'last_name'
                ],
                'INDEX'
            ),
            new Index(
                'column_email_index',
                [
                    'account_email'
                ],
                'INDEX'
            ),
            new Index(
                'column_contact_mobile_index',
                [
                    'contact_mobile'
                ],
                'INDEX'
            )
        ];
    }
}