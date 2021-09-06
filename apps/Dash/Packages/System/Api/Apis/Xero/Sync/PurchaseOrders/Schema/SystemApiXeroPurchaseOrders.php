<?php

namespace Apps\Dash\Packages\System\Api\Apis\Xero\Sync\PurchaseOrders\Schema;

use Phalcon\Db\Column;

class SystemApiXeroPurchaseOrders
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
                    'baz_po_id',
                    [
                        'type'          => Column::TYPE_INTEGER,
                        'notNull'       => false
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
                    'PurchaseOrderID',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 50,
                        'notNull'       => true
                    ]
                ),
                new Column(
                    'ContactID',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 50,
                        'notNull'       => true
                    ]
                ),
                new Column(
                    'PurchaseOrderNumber',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 50,
                        'notNull'       => true
                    ]
                ),
                new Column(
                    'Date',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 50,
                        'notNull'       => true
                    ]
                ),
                new Column(
                    'DeliveryDate',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 50,
                        'notNull'       => false
                    ]
                ),
                new Column(
                    'AttentionTo',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 50,
                        'notNull'       => false,
                    ]
                ),
                new Column(
                    'Telephone',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 15,
                        'notNull'       => false,
                    ]
                ),
                new Column(
                    'DeliveryInstructions',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 1024,
                        'notNull'       => false,
                    ]
                ),
                new Column(
                    'HasErrors',
                    [
                        'type'          => Column::TYPE_BOOLEAN,
                        'notNull'       => false,
                    ]
                ),
                new Column(
                    'IsDiscounted',
                    [
                        'type'          => Column::TYPE_BOOLEAN,
                        'notNull'       => false,
                    ]
                ),
                new Column(
                    'SentToContact',
                    [
                        'type'          => Column::TYPE_BOOLEAN,
                        'notNull'       => false,
                    ]
                ),
                new Column(
                    'Reference',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 50,
                        'notNull'       => false,
                    ]
                ),
                new Column(
                    'Type',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 50,
                        'notNull'       => false,
                    ]
                ),
                new Column(
                    'CurrencyRate',
                    [
                        'type'          => Column::TYPE_FLOAT,
                        'notNull'       => false,
                    ]
                ),
                new Column(
                    'CurrencyCode',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 3,
                        'notNull'       => false,
                    ]
                ),
                new Column(
                    'BrandingThemeID',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 50,
                        'notNull'       => false,
                    ]
                ),
                new Column(
                    'Status',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 20,
                        'notNull'       => true,
                    ]
                ),
                new Column(
                    'LineAmountTypes',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 20,
                        'notNull'       => false,
                    ]
                ),
                new Column(
                    'SubTotal',
                    [
                        'type'          => Column::TYPE_FLOAT,
                        'notNull'       => false,
                    ]
                ),
                new Column(
                    'TotalTax',
                    [
                        'type'          => Column::TYPE_FLOAT,
                        'notNull'       => false,
                    ]
                ),
                new Column(
                    'Total',
                    [
                        'type'          => Column::TYPE_FLOAT,
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
                    'HasAttachments',
                    [
                        'type'          => Column::TYPE_BOOLEAN,
                        'notNull'       => false,
                    ]
                ),
                new Column(
                    'DeliveryAddress',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 1024,
                        'notNull'       => false,
                    ]
                ),
                new Column(
                    'ExpectedArrivalDate',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 50,
                        'notNull'       => false,
                    ]
                ),
                new Column(
                    'ExpectedArrivalDateString',
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