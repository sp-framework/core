<?php

namespace Apps\Dash\Packages\System\Api\Apis\Xero\Sync\PurchaseOrders\Schema;

use Phalcon\Db\Column;

class SystemApiXeroPurchaseOrdersLineitems
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
                    'PurchaseOrderID',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 50,
                        'notNull' => false
                    ]
                ),
                new Column(
                    'LineItemID',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 50,
                        'notNull' => false
                    ]
                ),
                new Column(
                    'Description',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 1024,
                        'notNull' => false
                    ]
                ),
                new Column(
                    'Quantity',
                    [
                        'type'    => Column::TYPE_FLOAT,
                        'notNull' => false,
                    ]
                ),
                new Column(
                    'UnitAmount',
                    [
                        'type'    => Column::TYPE_FLOAT,
                        'notNull' => false,
                    ]
                ),
                new Column(
                    'ItemCode',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 50,
                        'notNull' => false
                    ]
                ),
                new Column(
                    'AccountID',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 50,
                        'notNull' => false
                    ]
                ),
                new Column(
                    'AccountCode',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 50,
                        'notNull' => false
                    ]
                ),
                new Column(
                    'Tracking',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 2048,
                        'notNull' => false
                    ]
                ),
                new Column(
                    'TaxType',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 50,
                        'notNull' => false
                    ]
                ),
                new Column(
                    'RepeatingInvoiceID',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 50,
                        'notNull' => false
                    ]
                ),
                new Column(
                    'TaxAmount',
                    [
                        'type'    => Column::TYPE_FLOAT,
                        'notNull' => false,
                    ]
                ),
                new Column(
                    'DiscountRate',
                    [
                        'type'    => Column::TYPE_FLOAT,
                        'notNull' => false,
                    ]
                ),
                new Column(
                    'LineAmount',
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