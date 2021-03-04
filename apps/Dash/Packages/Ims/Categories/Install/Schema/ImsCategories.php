<?php

namespace Apps\Dash\Packages\Ims\Categories\Install\Schema;

use Phalcon\Db\Column;

class ImsCategories
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
                    'hierarchy',
                    [
                        'type'    => Column::TYPE_TEXT,
                        'notNull' => false,
                    ]
                ),
                new Column(
                    'hierarchy_str',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 4096,
                        'notNull' => false,
                    ]
                ),
                new Column(
                    'hierarchy_level',
                    [
                        'type'    => Column::TYPE_TINYINTEGER,
                        'notNull' => false,
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
                    'parent_id',
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
                //         'size'    => 1024,
                //         'notNull' => false,
                //     ]
                // ),
                // new Column(
                //     'seo_meta_keywords',
                //     [
                //         'type'    => Column::TYPE_VARCHAR,
                //         'size'    => 2048,
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
                //         'size'    => 1024,
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