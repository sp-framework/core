<?php

namespace Apps\Dash\Packages\Hrms\Employees\Install\Schema;

use Phalcon\Db\Column;

class HrmsEmployees
{
    public function columns()
    {
        return
        [
           'columns' => [
                new Column(
                    'id',
                    [
                        'type'              => Column::TYPE_INTEGER,
                        'notNull'           => true,
                        'autoIncrement'     => true,
                        'primary'           => true,
                    ]
                ),
                new Column(
                    'portrait',
                    [
                        'type'              => Column::TYPE_VARCHAR,
                        'size'              => 1024,
                        'notNull'           => false,
                    ]
                ),
                new Column(
                    'account_id',
                    [
                        'type'              => Column::TYPE_VARCHAR,
                        'size'              => 100,
                        'notNull'           => true,
                    ]
                ),
                new Column(
                    'status',
                    [
                        'type'              => Column::TYPE_TINYINTEGER,
                        'notNull'           => true,
                    ]
                ),
                new Column(
                    'first_name',
                    [
                        'type'              => Column::TYPE_VARCHAR,
                        'size'              => 50,
                        'notNull'           => true,
                    ]
                ),
                new Column(
                    'last_name',
                    [
                        'type'              => Column::TYPE_VARCHAR,
                        'size'              => 50,
                        'notNull'           => true,
                    ]
                ),
                new Column(
                    'full_name',
                    [
                        'type'              => Column::TYPE_VARCHAR,
                        'size'              => 100,
                        'notNull'           => true,
                    ]
                ),
                new Column(
                    'designation',
                    [
                        'type'              => Column::TYPE_SMALLINTEGER,
                        'notNull'           => true,
                    ]
                ),
                new Column(
                    'manager_id',
                    [
                        'type'              => Column::TYPE_INTEGER,
                        'notNull'           => true,
                    ]
                ),
            ],
            'options' => [
                'TABLE_COLLATION' => 'utf8mb4_unicode_ci'
            ]
        ];
    }
}