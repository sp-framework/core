<?php

namespace Apps\Dash\Packages\System\Api\Apis\Xero\Sync\Items\Schema;

use Phalcon\Db\Column;

class SystemApiXeroItems
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
                    'baz_product_id',
                    [
                        'type'          => Column::TYPE_INTEGER,
                        'notNull'       => false,
                    ]
                ),
                new Column(
                    'api_id',
                    [
                        'type'          => Column::TYPE_TINYINTEGER,
                        'notNull'       => true
                    ]
                ),
                new Column(
                    'ItemID',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 50,
                        'notNull'       => true
                    ]
                ),
                new Column(
                    'Code',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 50,
                        'notNull'       => false
                    ]
                ),
                new Column(
                    'Name',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 100,
                        'notNull'       => false,
                    ]
                ),
                new Column(
                    'IsPurchased',
                    [
                        'type'          => Column::TYPE_BOOLEAN,
                        'notNull'       => false,
                    ]
                ),
                new Column(
                    'IsSold',
                    [
                        'type'          => Column::TYPE_BOOLEAN,
                        'notNull'       => false,
                    ]
                ),
                new Column(
                    'IsTrackedAsInventory',
                    [
                        'type'          => Column::TYPE_BOOLEAN,
                        'notNull'       => false,
                    ]
                ),
                new Column(
                    'InventoryAssetAccountCode',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 50,
                        'notNull'       => false,
                    ]
                ),
                new Column(
                    'TotalCostPool',
                    [
                        'type'          => Column::TYPE_FLOAT,
                        'notNull'       => false,
                    ]
                ),
                new Column(
                    'QuantityOnHand',
                    [
                        'type'          => Column::TYPE_FLOAT,
                        'notNull'       => false,
                    ]
                ),
                new Column(
                    'Description',
                    [
                        'type'          => Column::TYPE_TEXT,
                        'notNull'       => false,
                    ]
                ),
                new Column(
                    'PurchaseDescription',
                    [
                        'type'          => Column::TYPE_TEXT,
                        'notNull'       => false,
                    ]
                ),
                new Column(//Json Data
                    'SalesDetails',
                    [
                        'type'          => Column::TYPE_TEXT,
                        'notNull'       => false,
                    ]
                ),
                new Column(//Json Data
                    'PurchaseDetails',
                    [
                        'type'          => Column::TYPE_TEXT,
                        'notNull'       => false,
                    ]
                ),
                new Column(
                    'UpdatedDateUTC',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 50,
                        'notNull'       => false,
                    ]
                ),
                new Column(
                    'resync_local',
                    [
                        'type'          => Column::TYPE_BOOLEAN,
                        'notNull'       => false,
                    ]
                ),
                new Column(
                    'resync_remote',
                    [
                        'type'          => Column::TYPE_BOOLEAN,
                        'notNull'       => false,
                    ]
                ),
                new Column(
                    'conflict',
                    [
                        'type'          => Column::TYPE_BOOLEAN,
                        'notNull'       => false,
                    ]
                ),
                new Column(
                    'conflict_id',
                    [
                        'type'          => Column::TYPE_INTEGER,
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