<?php

namespace Apps\Dash\Packages\System\Api\Apis\Xero\Sync\PurchaseOrders\Schema;

use Phalcon\Db\Column;

class SystemApiXeroPurchaseOrdersHistoryRecords
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
                    'Details',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 2048,
                        'notNull' => false
                    ]
                ),
                new Column(
                    'Changes',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 2048,
                        'notNull' => false
                    ]
                ),
                new Column(
                    'User',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 100,
                        'notNull' => false,
                    ]
                ),
                new Column(
                    'DateUTC',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 50,
                        'notNull' => false,
                    ]
                ),
                new Column(
                    'DateUTCString',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 50,
                        'notNull' => false,
                    ]
                )
            ],
            'options' => [
                'TABLE_COLLATION' => 'utf8mb4_general_ci'
            ]
        ];
    }
}