<?php

namespace Apps\Dash\Packages\Business\Finances\Taxes\Install\Schema;

use Phalcon\Db\Column;

class BusinessFinancesTaxes
{
    public function columns()
    {
        return
        [
           'columns' => [
                new Column(
                    'id',
                    [
                        'type'          => Column::TYPE_SMALLINTEGER,
                        'notNull'       => true,
                        'autoIncrement' => true,
                        'primary'       => true,
                    ]
                ),
                new Column(
                    'name',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 50,
                        'notNull'       => true,
                    ]
                ),
                new Column(
                    'tax_group_id',
                    [
                        'type'          => Column::TYPE_TINYINTEGER,
                        'notNull'       => false,
                    ]
                ),
                new Column(
                    'amount',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 10,
                        'notNull'       => true,
                    ]
                ),
                new Column(
                    'description',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 2048,
                        'notNull'       => false,
                    ]
                )
            ],
            'options' => [
                'TABLE_COLLATION' => 'utf8mb4_general_ci'
            ]
        ];
    }
}