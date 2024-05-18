<?php

namespace System\Base\Installer\Packages\Setup\Schema\Basepackages;

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
                        'size'    => 50,
                        'notNull' => true,
                    ]
                ),
                new Column(
                    'component_id',
                    [
                        'type'    => Column::TYPE_SMALLINTEGER,
                        'size'    => 1,
                        'notNull' => true,
                    ]
                ),
                new Column(
                    'conditions',
                    [
                        'type'    => Column::TYPE_TEXT,
                        'notNull' => false,
                    ]
                ),
                new Column(
                    'filter_type',
                    [
                        'type'    => Column::TYPE_TINYINTEGER,
                        'size'    => 1,
                        'notNull' => true,
                    ]
                ),
                new Column(
                    'auto_generated',
                    [
                        'type'    => Column::TYPE_TINYINTEGER,
                        'size'    => 1,
                        'notNull' => false,
                    ]
                ),
                new Column(
                    'is_default',
                    [
                        'type'    => Column::TYPE_TINYINTEGER,
                        'size'    => 1,
                        'notNull' => true,
                    ]
                ),
                new Column(
                    'account_id',
                    [
                        'type'    => Column::TYPE_INTEGER,
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