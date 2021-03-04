<?php

namespace Apps\Dash\Packages\System\Api\Ebay\Taxonomy\Install\Schema;

use Phalcon\Db\Column;
use Phalcon\Db\Index;

class SystemApiEbayTaxonomy
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
                    'name',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 100,
                        'notNull' => true,
                    ]
                ),
                new Column(
                    'hierarchy',
                    [
                        'type'    => Column::TYPE_TEXT,
                        'notNull' => true,
                    ]
                ),
                new Column(
                    'hierarchy_str',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 4096,
                        'notNull' => true,
                    ]
                ),
                new Column(
                    'installed',
                    [
                        'type'    => Column::TYPE_TINYINTEGER,
                        'notNull' => true,
                    ]
                ),
                new Column(
                    'enabled',
                    [
                        'type'    => Column::TYPE_TINYINTEGER,
                        'notNull' => true,
                    ]
                ),
                new Column(
                    'root_id',
                    [
                        'type'    => Column::TYPE_INTEGER,
                        'notNull' => true,
                    ]
                ),
                new Column(
                    'parent',
                    [
                        'type'    => Column::TYPE_INTEGER,
                        'notNull' => false,
                    ]
                ),
                new Column(
                    'has_childs',
                    [
                        'type'    => Column::TYPE_TINYINTEGER,
                        'notNull' => false,
                    ]
                ),
                new Column(
                    'taxonomy_version',
                    [
                        'type'    => Column::TYPE_SMALLINTEGER,
                        'notNull' => true,
                    ]
                ),
                new Column(
                    'product_count',
                    [
                        'type'    => Column::TYPE_INTEGER,
                        'notNull' => true,
                    ]
                )
            ],
            'options' => [
                'TABLE_COLLATION' => 'utf8mb4_unicode_ci'
            ]
        ];
    }
}