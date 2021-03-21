<?php

namespace Apps\Dash\Packages\Ims\Stock\PurchaseOrders\Install\Schema;

use Phalcon\Db\Column;

class ImsStockPurchaseOrders
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
                    'entity_id',
                    [
                        'type'    => Column::TYPE_TINYINTEGER,
                        'notNull' => true,
                    ]
                ),
                new Column(
                    'reference_orders',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 100,
                        'notNull' => false
                    ]
                ),
                new Column(
                    'expected_delivery_date',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 12,
                        'notNull' => false,
                    ]
                ),
                new Column(
                    'status',
                    [
                        'type'    => Column::TYPE_TINYINTEGER,
                        'notNull' => true,
                    ]
                ),
                new Column(
                    'vendor_id',
                    [
                        'type'    => Column::TYPE_INTEGER,
                        'notNull' => true,
                    ]
                ),
                new Column(
                    'vendor_address_id',
                    [
                        'type'    => Column::TYPE_INTEGER,
                        'notNull' => true,
                    ]
                ),
                new Column(
                    'vendor_contact_id',
                    [
                        'type'    => Column::TYPE_INTEGER,
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