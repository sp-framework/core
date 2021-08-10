<?php

namespace Apps\Dash\Packages\Business\Directory\Vendors\Install\Schema;

use Phalcon\Db\Column;

class VendorsFinancialDetails
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
                    'vendor_id',
                    [
                        'type'          => Column::TYPE_INTEGER,
                        'notNull'       => false,
                    ]
                ),
                new Column(
                    'acn',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 9,
                        'notNull'       => false,
                    ]
                ),
                new Column(
                    'currency',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 3,
                        'notNull'       => true,
                    ]
                ),
                new Column(
                    'bsb',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 8,
                        'notNull'       => false,
                    ]
                ),
                new Column(
                    'account_number',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 20,
                        'notNull'       => false,
                    ]
                ),
                new Column(
                    'swift_code',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 20,
                        'notNull'       => false,
                    ]
                ),
                new Column(
                    'bills_due_day',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 4,
                        'notNull'       => false,
                    ]
                ),
                new Column(
                    'bills_due_day_term',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 20,
                        'notNull'       => false,
                    ]
                ),
                new Column(
                    'bills_tax_enabled',
                    [
                        'type'          => Column::TYPE_TINYINTEGER,
                        'notNull'       => false,
                    ]
                ),
                new Column(
                    'bills_tax_id',
                    [
                        'type'          => Column::TYPE_TINYINTEGER,
                        'notNull'       => false,
                    ]
                ),
                new Column(
                    'invoices_due_day',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 4,
                        'notNull'       => false,
                    ]
                ),
                new Column(
                    'invoices_due_day_term',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 20,
                        'notNull'       => false,
                    ]
                ),
                new Column(
                    'invoices_tax_enabled',
                    [
                        'type'          => Column::TYPE_TINYINTEGER,
                        'notNull'       => false,
                    ]
                ),
                new Column(
                    'invoices_tax_id',
                    [
                        'type'          => Column::TYPE_TINYINTEGER,
                        'notNull'       => false,
                    ]
                ),
                new Column(
                    'credit_limit_amount',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 20,
                        'notNull'       => false,
                    ]
                ),
                new Column(
                    'credit_limit_block',
                    [
                        'type'          => Column::TYPE_TINYINTEGER,
                        'notNull'       => false,
                    ]
                ),
                new Column(
                    'discount',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 3,
                        'notNull'       => false,
                    ]
                ),
            ],
            'options' => [
                'TABLE_COLLATION' => 'utf8mb4_general_ci'
            ]
        ];
    }
}