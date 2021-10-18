<?php

namespace Apps\Dash\Packages\Hrms\Employees\Install\Schema;

use Phalcon\Db\Column;

class HrmsEmployeesContact
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
                    'work_type_id',
                    [
                        'type'              => Column::TYPE_TINYINTEGER,
                        'notNull'           => true,
                    ]
                ),
                new Column(
                    'contact_location_id',
                    [
                        'type'              => Column::TYPE_SMALLINTEGER,
                        'notNull'           => false,
                    ]
                ),
                new Column(
                    'contact_phone',
                    [
                        'type'              => Column::TYPE_VARCHAR,
                        'size'              => 100,
                        'notNull'           => true,
                    ]
                ),
                new Column(
                    'contact_phone_ext',
                    [
                        'type'              => Column::TYPE_VARCHAR,
                        'size'              => 20,
                        'notNull'           => false,
                    ]
                ),
                new Column(
                    'contact_mobile',
                    [
                        'type'              => Column::TYPE_VARCHAR,
                        'size'              => 100,
                        'notNull'           => true,
                    ]
                ),
                new Column(
                    'contact_fax',
                    [
                        'type'              => Column::TYPE_VARCHAR,
                        'size'              => 100,
                        'notNull'           => false,
                    ]
                ),
                new Column(
                    'contact_other',
                    [
                        'type'              => Column::TYPE_VARCHAR,
                        'size'              => 200,
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