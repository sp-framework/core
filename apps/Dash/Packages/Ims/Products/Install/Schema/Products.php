<?php

namespace Apps\Dash\Packages\Ims\Products\Install\Schema;

use Phalcon\Db\Column;
use Phalcon\Db\Index;

class Products
{
    public function columns()
    {
        return
        [
           'columns' => [
                new Column(
                    'id',
                    [
                        'type'          => Column::TYPE_MEDIUMINTEGER,
                        'notNull'       => true,
                        'autoIncrement' => true,
                        'primary'       => true,
                    ]
                ),
                new Column(
                    'product_type',
                    [
                        'type'    => Column::TYPE_TINYINTEGER,
                        'notNull' => true,
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
                    'code_sku',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 50,
                        'notNull' => false,
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
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 8192,
                        'notNull' => false,
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
                    'subtitle',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 80,
                        'notNull' => false,
                    ]
                ),
                new Column(
                    'manufacturer',
                    [
                        'type'    => Column::TYPE_INTEGER,
                        'notNull' => false,
                    ]
                ),
                new Column(
                    'brand',
                    [
                        'type'    => Column::TYPE_INTEGER,
                        'notNull' => false,
                    ]
                ),
                new Column(
                    'description',
                    [
                        'type'    => Column::TYPE_MEDIUMTEXT,
                        'notNull' => false,
                    ]
                ),
                new Column(
                    'images',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 4096,
                        'notNull' => false
                    ]
                ),
                new Column(
                    'downloadables',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 4096,
                        'notNull' => false
                    ]
                ),
            ],
            'options' => [
                'TABLE_COLLATION' => 'utf8mb4_general_ci'
            ],
            'indexes' => [
                new Index(
                    'column_UNIQUE',
                    [
                        'title'
                    ],
                    'UNIQUE'
                )
            ]
        ];
    }
}