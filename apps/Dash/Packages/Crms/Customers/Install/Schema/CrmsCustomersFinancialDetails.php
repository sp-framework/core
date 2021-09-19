<?php

namespace Apps\Dash\Packages\Crms\Customers\Install\Schema;

use Phalcon\Db\Column;

class CrmsCustomersFinancialDetails
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
                    'customer_id',
                    [
                        'type'          => Column::TYPE_INTEGER,
                        'notNull'       => true,
                        'comment'       => 'Customer ID of this row'
                    ]
                ),
                new Column(
                    'abn',
                    [
                        'type'          => Column::TYPE_BIGINTEGER,
                        'size'          => 11,
                        'notNull'       => false,
                        'comment'       => 'Australia Business Number'
                    ]
                ),
                new Column(
                    'currency',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 3,
                        'notNull'       => false,
                        'comment'       => 'Currency'
                    ]
                ),
                new Column(
                    'bsb',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 8,
                        'notNull'       => false,
                        'comment'       => 'BSB'
                    ]
                ),
                new Column(
                    'account_number',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 20,
                        'notNull'       => false,
                        'comment'       => 'Account Number'
                    ]
                ),
                new Column(
                    'swift_code',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 20,
                        'notNull'       => false,
                        'comment'       => 'Bank Swift code'
                    ]
                ),
                new Column(
                    'invoices_due_day',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 4,
                        'notNull'       => false,
                        'comment'       => 'Invoice Due Day of the month from start of month'
                    ]
                ),
                new Column(
                    'invoices_due_day_term',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 20,
                        'notNull'       => false,
                        'comment'       => 'Invoice Due Day term'
                    ]
                ),
                new Column(
                    'invoices_tax_enabled',
                    [
                        'type'          => Column::TYPE_TINYINTEGER,
                        'notNull'       => false,
                        'comment'       => 'Invoice tax enabled'
                    ]
                ),
                new Column(
                    'invoices_tax_id',
                    [
                        'type'          => Column::TYPE_TINYINTEGER,
                        'notNull'       => false,
                        'comment'       => 'Invoice tax ID from package Taxes'
                    ]
                ),
                new Column(
                    'credit_limit_amount',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 20,
                        'notNull'       => false,
                        'comment'       => 'Credit limit'
                    ]
                ),
                new Column(
                    'credit_limit_block',
                    [
                        'type'          => Column::TYPE_TINYINTEGER,
                        'notNull'       => false,
                        'comment'       => 'Block generating invoices for customer after credit limit reached'
                    ]
                ),
                new Column(
                    'invoice_discount',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 3,
                        'notNull'       => false,
                        'comment'       => 'Discount provided'
                    ]
                ),
                new Column(
                    'cc_details',
                    [
                        'type'          => Column::TYPE_TEXT,
                        'notNull'       => false,
                        'comment'       => 'Encrypted CC Details'
                    ]
                ),
            ],
            'options' => [
                'TABLE_COLLATION' => 'utf8mb4_general_ci'
            ]
        ];
    }
}