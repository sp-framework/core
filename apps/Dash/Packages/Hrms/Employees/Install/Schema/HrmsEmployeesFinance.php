<?php

namespace Apps\Dash\Packages\Hrms\Employees\Install\Schema;

use Phalcon\Db\Column;

class HrmsEmployeesFinance
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
                    'employee_id',
                    [
                        'type'              => Column::TYPE_INTEGER,
                        'notNull'           => true,
                    ]
                ),
                new Column(
                    'tfn',
                    [
                        'type'              => Column::TYPE_VARCHAR,
                        'size'              => 9,
                        'notNull'           => false,
                    ]
                ),
                new Column(
                    'currency',
                    [
                        'type'              => Column::TYPE_VARCHAR,
                        'size'              => 3,
                        'notNull'           => true,
                    ]
                ),
                new Column(
                    'bsb',
                    [
                        'type'              => Column::TYPE_VARCHAR,
                        'size'              => 8,
                        'notNull'           => false,
                    ]
                ),
                new Column(
                    'account_number',
                    [
                        'type'              => Column::TYPE_VARCHAR,
                        'size'              => 20,
                        'notNull'           => false,
                    ]
                ),
                new Column(
                    'swift_code',
                    [
                        'type'              => Column::TYPE_VARCHAR,
                        'size'              => 20,
                        'notNull'           => false,
                    ]
                ),
            ],
            'options' => [
                'TABLE_COLLATION' => 'utf8mb4_unicode_ci'
            ]
        ];
    }
}