<?php

namespace Applications\Dash\Packages\Ims\Categories\Install\Schema;

use Phalcon\Db\Column;

class Categories
{
    public function columns()
    {
        return
        [
           'columns' => [
                new Column(
                    'id',
                    [
                        'type'          => Column::TYPE_TINYINTEGER,
                        'notNull'       => true,
                        'autoIncrement' => true,
                        'primary'       => true,
                    ]
                ),
                new Column(
                    'image',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 1024,
                        'notNull' => false,
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
                    'description',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 1024,
                        'notNull' => false,
                    ]
                ),
                new Column(
                    'type',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 15,
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
                    'product_count',
                    [
                        'type'    => Column::TYPE_INTEGER,
                        'notNull' => false,
                    ]
                ),
                new Column(
                    'category',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 2048,
                        'notNull' => true,
                    ]
                ),
                new Column(
                    'sequence',
                    [
                        'type'    => Column::TYPE_INTEGER,
                        'notNull' => false,
                    ]
                ),
                new Column(
                    'need_auth',
                    [
                        'type'    => Column::TYPE_TINYINTEGER,
                        'notNull' => false,
                    ]
                ),
                new Column(
                    'visible_to_role_ids',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 1024,
                        'notNull' => false,
                    ]
                ),
                new Column(
                    'visible_on_channel_ids',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 1024,
                        'notNull' => false
                    ]
                ),
                // new Column(
                //     'seo_title',
                //     [
                //         'type'    => Column::TYPE_VARCHAR,
                //         'size'    => 100,
                //         'notNull' => false,
                //     ]
                // ),
                // new Column(
                //     'seo_meta_keywords',
                //     [
                //         'type'    => Column::TYPE_VARCHAR,
                //         'size'    => 1024,
                //         'notNull' => false,
                //     ]
                // ),
                // new Column(
                //     'seo_meta_description',
                //     [
                //         'type'    => Column::TYPE_VARCHAR,
                //         'size'    => 1024,
                //         'notNull' => false,
                //     ]
                // ),
                // new Column(
                //     'seo_heading',
                //     [
                //         'type'    => Column::TYPE_VARCHAR,
                //         'size'    => 100,
                //         'notNull' => false,
                //     ]
                // ),
            ],
            'options' => [
                'TABLE_COLLATION' => 'utf8mb4_general_ci'
            ]
        ];
    }
}