<?php

namespace Applications\Admin\Packages\Filters\Install\Schema;

use Phalcon\Db\Column;

class Filters
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
                    'name',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 50,
                        'notNull' => true,
                    ]
                ),
                new Column(
                    'conditions',
                    [
                        'type'    => Column::TYPE_TEXT,
                        'notNull' => true,
                    ]
                ),
                new Column(
                    'component_id',
                    [
                        'type'    => Column::TYPE_TINYINTEGER,
                        'size'    => 1,
                        'notNull' => true,
                    ]
                ),
                new Column(
                    'permission',
                    [
                        'type'    => Column::TYPE_TINYINTEGER,
                        'size'    => 1,
                        'notNull' => true,
                    ]
                ),
                new Column(
                    'shared_ids',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 4096,
                        'notNull' => false,
                    ]
                ),
            ]
        ];
    }
}