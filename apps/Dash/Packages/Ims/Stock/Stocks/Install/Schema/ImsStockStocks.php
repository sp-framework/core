<?php

namespace Apps\Dash\Packages\Ims\Stock\Stocks\Install\Schema;

use Phalcon\Db\Column;

class ImsStockStocks
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
                    'code_mpn',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 50,
                        'notNull' => true,
                    ]
                ),
                new Column(
                    'code_ean',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 13,
                        'notNull' => false,
                    ]
                ),
                new Column(
                    'code_ean_barcode',
                    [
                        'type'    => Column::TYPE_TEXT,
                        'notNull' => false,
                    ]
                ),
                new Column(
                    'code_sku',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 50,
                        'notNull' => true,
                    ]
                ),
                new Column(
                    'code_supplier_sku',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 50,
                        'notNull' => true,
                    ]
                ),
                new Column(
                    'title',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 80,
                        'notNull' => true,
                    ]
                ),
                new Column(
                    'unassigned_stock',
                    [
                        'type'    => Column::TYPE_INTEGER,
                        'notNull' => true,
                    ]
                ),
                new Column(
                    'assigned_stock',
                    [
                        'type'    => Column::TYPE_INTEGER,
                        'notNull' => true,
                    ]
                ),
                new Column(
                    'po_pending_stock',
                    [
                        'type'    => Column::TYPE_INTEGER,
                        'notNull' => true,
                    ]
                ),
                new Column(
                    'supplier_stock',
                    [
                        'type'    => Column::TYPE_INTEGER,
                        'notNull' => true,
                    ]
                ),
                new Column(
                    'location_id',
                    [
                        'type'    => Column::TYPE_INTEGER,
                        'notNull' => true,
                    ]
                ),
                new Column(
                    'filename',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 100,
                        'notNull' => true,
                    ]
                ),
                new Column(
                    'content',
                    [
                        'type'    => Column::TYPE_TEXT,
                        'notNull' => true,
                    ]
                )
            ],
            'options' => [
                'TABLE_COLLATION' => 'utf8mb4_general_ci'
            ]
        ];
    }
}