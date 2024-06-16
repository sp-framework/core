<?php

namespace System\Base\Installer\Packages\Setup\Schema\Basepackages;

use Phalcon\Db\Column;
use Phalcon\Db\Index;

class Maintenance
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
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 50,
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
                ),
                new Column(
                    'status',
                    [
                        'type'              => Column::TYPE_BOOLEAN,
                        'notNull'           => true
                    ]
                ),
                new Column(
                    'state',// - Scheduled, running, complete'
                    [
                        'type'              => Column::TYPE_TINYINTEGER,
                        'notNull'           => true
                    ]
                ),
                new Column(
                    'Reference',// - External reference when running maintenance on core'
                    [
                        'type'              => Column::TYPE_VARCHAR,
                        'size'              => 50,
                        'notNull'           => false
                    ]
                ),
                new Column(
                    'start_at',
                    [
                        'type'              => Column::TYPE_VARCHAR,
                        'size'              => 50,
                        'notNull'           => true
                    ]
                ),
                new Column(
                    'end_at',
                    [
                        'type'              => Column::TYPE_VARCHAR,
                        'size'              => 50,
                        'notNull'           => true
                    ]
                ),
                new Column(
                    'level_of_maintenance',// Core, AppType, App, Component',
                    [
                        'type'              => Column::TYPE_TINYINTEGER,
                        'notNull'           => true
                    ]
                ),
                new Column(
                    'level_of_maintenance_modules',// Core, AppType, App, Component',
                    [
                        'type'              => Column::TYPE_VARCHAR,
                        'size'              => 1024,
                        'notNull'           => false
                    ]
                ),
                new Column(
                    'allow_from_ip_addresses',
                    [
                        'type'              => Column::TYPE_VARCHAR,
                        'size'              => 2048,
                        'notNull'           => true
                    ]
                ),
                new Column(
                    'notification_email_groups',
                    [
                        'type'              => Column::TYPE_VARCHAR,
                        'size'              => 1024,
                        'notNull'           => false
                    ]
                ),
                new Column(
                    'notification_email_users',
                    [
                        'type'              => Column::TYPE_VARCHAR,
                        'size'              => 1024,
                        'notNull'           => false
                    ]
                ),
                new Column(
                    'notification_email_others',
                    [
                        'type'              => Column::TYPE_VARCHAR,
                        'size'              => 2048,
                        'notNull'           => false
                    ]
                ),
                new Column(
                    'maintenance_template_id',//default will be used if not provided.
                    [
                        'type'              => Column::TYPE_INTEGER,
                        'notNull'           => false
                    ]
                ),
                new Column(
                    'maintenance_email_template_id',//default will be used if not provided.
                    [
                        'type'              => Column::TYPE_INTEGER,
                        'notNull'           => false
                    ]
                )
            ],
            'options' => [
                'TABLE_COLLATION' => 'utf8mb4_general_ci'
            ]
        ];
    }

    public function indexes()
    {
        return
        [
            new Index(
                'status_index',
                [
                    'status'
                ],
                'INDEX'
            ),
        ];
    }
}
