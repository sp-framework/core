<?php

namespace Apps\Dash\Packages\Ims\Stock\PurchaseOrders\Install\Schema;

use Phalcon\Db\Column;

class ImsStockPurchaseOrdersProducts
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
                        'type'    => Column::TYPE_INTEGER,
                        'notNull' => true,
                    ]
                ),
                new Column(
                    'seq',
                    [
                        'type'    => Column::TYPE_TINYINTEGER,
                        'notNull' => true
                    ]
                ),
                new Column(
                    'mpn',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 50,
                        'notNull' => true,
                    ]
                ),
                new Column(
                    'product_title',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 100,
                        'notNull' => true,
                    ]
                ),
                new Column(
                    'use_vendor_tax',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 20,
                        'notNull' => true,
                    ]
                ),
                new Column(
                    'tax',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 50,
                        'notNull' => true,
                    ]
                ),
                new Column(
                    'tax_rate',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 20,
                        'notNull' => true,
                    ]
                ),
                new Column(
                    'use_vendor_discount',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 20,
                        'notNull' => true,
                    ]
                ),
                new Column(
                    'product_discount',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 20,
                        'notNull' => false,
                    ]
                ),
                new Column(
                    'product_discount_rate',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 20,
                        'notNull' => false,
                    ]
                ),
                new Column(
                    'product_qty',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 20,
                        'notNull' => true,
                    ]
                ),
                new Column(
                    'product_unit_price',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 20,
                        'notNull' => true,
                    ]
                ),
                new Column(
                    'product_unit_price_incl_tax',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 10,
                        'notNull' => true,
                    ]
                ),
                new Column(
                    'product_amount',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 20,
                        'notNull' => true,
                    ]
                ),
            ],
            'options' => [
                'TABLE_COLLATION' => 'utf8mb4_general_ci'
            ]
        ];
    }
}