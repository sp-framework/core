<?php

namespace Apps\Dash\Packages\System\Api\Apis\Xero\Sync\ContactGroups\Schema;

use Phalcon\Db\Column;

class SystemApiXeroContactGroups
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
                    'api_id',
                    [
                        'type'          => Column::TYPE_TINYINTEGER,
                        'notNull'       => true
                    ]
                ),
                new Column(
                    'baz_vendor_group_id',
                    [
                        'type'          => Column::TYPE_SMALLINTEGER,
                        'notNull'       => false
                    ]
                ),
                new Column(
                    'baz_customer_group_id',
                    [
                        'type'          => Column::TYPE_SMALLINTEGER,
                        'notNull'       => false
                    ]
                ),
                new Column(
                    'ContactGroupID',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 50,
                        'notNull'       => false
                    ]
                ),
                new Column(
                    'Name',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 100,
                        'notNull'       => false
                    ]
                ),
                new Column(
                    'Status',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 50,
                        'notNull'       => false
                    ]
                ),
                new Column(
                    'HasValidationErrors',
                    [
                        'type'          => Column::TYPE_BOOLEAN,
                        'notNull'       => false
                    ]
                ),
                new Column(
                    'resync_local',
                    [
                        'type'          => Column::TYPE_BOOLEAN,
                        'notNull'       => false,
                    ]
                ),
                new Column(
                    'resync_remote',
                    [
                        'type'          => Column::TYPE_BOOLEAN,
                        'notNull'       => false,
                    ]
                ),
                new Column(
                    'conflict',
                    [
                        'type'          => Column::TYPE_BOOLEAN,
                        'notNull'       => false,
                    ]
                ),
                new Column(
                    'conflict_id',
                    [
                        'type'          => Column::TYPE_INTEGER,
                        'notNull'       => false,
                    ]
                ),
            ],
            'options' => [
                'TABLE_COLLATION' => 'utf8mb4_general_ci'
            ]
        ];
    }
}