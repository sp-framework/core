<?php

namespace Apps\Dash\Packages\Ims\Stock\PurchaseOrders\Install\Schema;

use Phalcon\Db\Column;

class ImsStockPurchaseOrdersXero
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
                    'purchase_order_id',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 50,
                        'notNull' => false
                    ]
                ),
                new Column(
                    'purchase_order_number',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 50,
                        'notNull' => false
                    ]
                ),
                new Column(
                    'date',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 50,
                        'notNull' => false
                    ]
                ),
                new Column(
                    'delivery_date',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 50,
                        'notNull' => false
                    ]
                ),
                new Column(
                    'attention_to',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 50,
                        'notNull' => false,
                    ]
                ),
                new Column(
                    'telephone',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 15,
                        'notNull' => false,
                    ]
                ),
                new Column(
                    'delivery_instructions',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 1024,
                        'notNull' => false,
                    ]
                ),
                new Column(
                    'has_errors',
                    [
                        'type'    => Column::TYPE_BOOLEAN,
                        'notNull' => false,
                    ]
                ),
                new Column(
                    'is_discounted',
                    [
                        'type'    => Column::TYPE_BOOLEAN,
                        'notNull' => false,
                    ]
                ),
                new Column(
                    'reference',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 50,
                        'notNull' => false,
                    ]
                ),
                new Column(
                    'type',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 50,
                        'notNull' => false,
                    ]
                ),
                new Column(
                    'currency_rate',
                    [
                        'type'    => Column::TYPE_FLOAT,
                        'notNull' => false,
                    ]
                ),
                new Column(
                    'currency_code',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 3,
                        'notNull' => false,
                    ]
                ),
                new Column(
                    'branding_theme_id',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 50,
                        'notNull' => false,
                    ]
                ),
                new Column(
                    'status',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 20,
                        'notNull' => false,
                    ]
                ),
                new Column(
                    'line_amount_types',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 20,
                        'notNull' => false,
                    ]
                ),
                new Column(
                    'sub_total',
                    [
                        'type'    => Column::TYPE_FLOAT,
                        'notNull' => false,
                    ]
                ),
                new Column(
                    'total_tax',
                    [
                        'type'    => Column::TYPE_FLOAT,
                        'notNull' => false,
                    ]
                ),
                new Column(
                    'total',
                    [
                        'type'    => Column::TYPE_FLOAT,
                        'notNull' => false,
                    ]
                ),
                new Column(
                    'updated_date_utc',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 50,
                        'notNull' => false,
                    ]
                ),
                new Column(
                    'has_attachments',
                    [
                        'type'    => Column::TYPE_BOOLEAN,
                        'notNull' => false,
                    ]
                ),
                new Column(
                    'delivery_address',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 1024,
                        'notNull' => false,
                    ]
                ),
                new Column(
                    'expected_arrival_date',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 50,
                        'notNull' => false,
                    ]
                ),
                new Column(
                    'expected_arrival_date_string',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 50,
                        'notNull' => false,
                    ]
                ),
            ],
            'options' => [
                'TABLE_COLLATION' => 'utf8mb4_general_ci'
            ]
        ];
    }
}