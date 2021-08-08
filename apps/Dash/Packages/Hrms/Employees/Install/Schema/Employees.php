<?php

namespace Apps\Dash\Packages\Hrms\Employees\Install\Schema;

use Phalcon\Db\Column;

class Employees
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
                        'type'              => Column::TYPE_INTEGER,
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
                        'size'              => 100,
                        'notNull'           => true,
                    ]
                ),
                new Column(
                    'last_name',
                    [
                        'type'              => Column::TYPE_VARCHAR,
                        'size'              => 100,
                        'notNull'           => true,
                    ]
                ),
                new Column(
                    'full_name',
                    [
                        'type'              => Column::TYPE_VARCHAR,
                        'size'              => 200,
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
                new Column(
                    'type_id',
                    [
                        'type'              => Column::TYPE_TINYINTEGER,
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
                ),
                new Column(
                    'contact_location_id',
                    [
                        'type'              => Column::TYPE_SMALLINTEGER,
                        'notNull'           => false,
                    ]
                ),
                new Column(
                    'contact_address_id',
                    [
                        'type'              => Column::TYPE_INTEGER,
                        'notNull'           => false,
                    ]
                ),
                new Column(
                    'contact_phone',
                    [
                        'type'              => Column::TYPE_VARCHAR,
                        'size'              => 15,
                        'notNull'           => true,
                    ]
                ),
                new Column(
                    'contact_phone_ext',
                    [
                        'type'              => Column::TYPE_VARCHAR,
                        'size'              => 10,
                        'notNull'           => false,
                    ]
                ),
                new Column(
                    'contact_mobile',
                    [
                        'type'              => Column::TYPE_VARCHAR,
                        'size'              => 15,
                        'notNull'           => true,
                    ]
                ),
                new Column(
                    'contact_fax',
                    [
                        'type'              => Column::TYPE_VARCHAR,
                        'size'              => 15,
                        'notNull'           => false,
                    ]
                ),
                new Column(
                    'contact_other',
                    [
                        'type'              => Column::TYPE_VARCHAR,
                        'size'              => 15,
                        'notNull'           => false,
                    ]
                ),
                new Column(
                    'contact_notes',
                    [
                        'type'              => Column::TYPE_VARCHAR,
                        'size'              => 2048,
                        'notNull'           => false,
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
                // new Column(
                //     'skills',
                //     [
                //         'type'    => Column::TYPE_VARCHAR,
                //         'size'    => 4096,
                //         'notNull' => false,
                //     ]
                // ),
                // new Column(
                //     'skills_attachments',
                //     [
                //         'type'    => Column::TYPE_VARCHAR,
                //         'size'    => 2048,
                //         'notNull' => false,
                //     ]
                // ),
            ],
            'options' => [
                'TABLE_COLLATION' => 'utf8mb4_unicode_ci'
            ]
        ];
    }
}