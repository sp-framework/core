<?php

namespace Apps\Dash\Packages\Ims\Stock\PurchaseOrders\Install\Schema;

use Phalcon\Db\Column;

class ImsStockPurchaseOrdersXeroLineitems
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
                    'line_item_id',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 50,
                        'notNull' => false
                    ]
                ),
                new Column(
                    'description',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 1024,
                        'notNull' => false
                    ]
                ),
                new Column(
                    'quantity',
                    [
                        'type'    => Column::TYPE_FLOAT,
                        'notNull' => false,
                    ]
                ),
                new Column(
                    'unit_amount',
                    [
                        'type'    => Column::TYPE_FLOAT,
                        'notNull' => false,
                    ]
                ),
                new Column(
                    'item_code',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 50,
                        'notNull' => false
                    ]
                ),
                new Column(
                    'amount_code',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 50,
                        'notNull' => false
                    ]
                ),
                new Column(
                    'tax_type',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 50,
                        'notNull' => false
                    ]
                ),
                new Column(
                    'repeating_invoice_id',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 50,
                        'notNull' => false
                    ]
                ),
                new Column(
                    'tax_amount',
                    [
                        'type'    => Column::TYPE_FLOAT,
                        'notNull' => false,
                    ]
                ),
                new Column(
                    'discount_rate',
                    [
                        'type'    => Column::TYPE_FLOAT,
                        'notNull' => false,
                    ]
                ),
                new Column(
                    'discount_amount',
                    [
                        'type'    => Column::TYPE_FLOAT,
                        'notNull' => false,
                    ]
                ),
                new Column(
                    'line_amount',
                    [
                        'type'    => Column::TYPE_FLOAT,
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