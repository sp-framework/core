<?php

namespace Apps\Dash\Packages\Hrms\Employees\Install\Schema;

use Phalcon\Db\Column;

class HrmsEmployeesEmployment
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
                    'employment_type_id',
                    [
                        'type'              => Column::TYPE_TINYINTEGER,
                        'notNull'           => true,
                    ]
                ),
                new Column(
                    'contractor_vendor_id',
                    [
                        'type'               => Column::TYPE_INTEGER,
                        'notNull'            => false,
                    ]
                ),
                new Column(
                    'hire_date',
                    [
                        'type'              => Column::TYPE_VARCHAR,
                        'size'              => 12,
                        'notNull'           => true,
                    ]
                ),
                new Column(
                    'terminate_date',
                    [
                        'type'              => Column::TYPE_VARCHAR,
                        'size'              => 12,
                        'notNull'           => false,
                    ]
                ),
                new Column(
                    'hire_manager_id',
                    [
                        'type'              => Column::TYPE_INTEGER,
                        'notNull'           => false,
                    ]
                ),
                new Column(
                    'hire_referrer_id',
                    [
                        'type'              => Column::TYPE_INTEGER,
                        'notNull'           => false,
                    ]
                ),
                new Column(
                    'employment_attachments',
                    [
                        'type'              => Column::TYPE_VARCHAR,
                        'size'              => 2048,
                        'notNull'           => false,
                    ]
                ),
                new Column(
                    'employment_notes',
                    [
                        'type'              => Column::TYPE_VARCHAR,
                        'size'              => 2048,
                        'notNull'           => false,
                    ]
                )
            ],
            'options' => [
                'TABLE_COLLATION' => 'utf8mb4_unicode_ci'
            ]
        ];
    }
}