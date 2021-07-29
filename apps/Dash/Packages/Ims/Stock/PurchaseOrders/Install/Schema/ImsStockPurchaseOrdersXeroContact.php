<?php

namespace Apps\Dash\Packages\Ims\Stock\PurchaseOrders\Install\Schema;

use Phalcon\Db\Column;

class ImsStockPurchaseOrdersXeroContact
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
                    'contact_id',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 50,
                        'notNull' => false
                    ]
                ),
                new Column(
                    'contact_status',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 20,
                        'notNull' => false
                    ]
                ),
                new Column(
                    'name',//Business_name
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 100,
                        'notNull' => false,
                    ]
                ),
                new Column(
                    'default_currency',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 3,
                        'notNull' => false
                    ]
                ),
                new Column(
                    'updated_date_utc',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 50,
                        'notNull' => false
                    ]
                ),
                new Column(
                    'has_validation_errors',
                    [
                        'type'    => Column::TYPE_BOOLEAN,
                        'notNull' => false
                    ]
                )
            ],
            'options' => [
                'TABLE_COLLATION' => 'utf8mb4_general_ci'
            ]
        ];
    }
}