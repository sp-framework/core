<?php

namespace Apps\Dash\Packages\Business\Entities\Install\Schema;

use Phalcon\Db\Column;

class Entities
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
                    'logo',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 1024,
                        'notNull' => false,
                    ]
                ),
                new Column(
                    'abn',
                    [
                        'type'    => Column::TYPE_BIGINTEGER,
                        'size'    => 11,
                        'notNull' => false,
                    ]
                ),
                new Column(
                    'business_name',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 512,
                        'notNull' => true,
                    ]
                ),
                new Column(
                    'entity_type',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 10,
                        'notNull' => true,
                    ]
                ),
                new Column(
                    'address_id',
                    [
                        'type'    => Column::TYPE_INTEGER,
                        'notNull' => true,
                    ]
                ),
                new Column(
                    'api_id',
                    [
                        'type'    => Column::TYPE_INTEGER,
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