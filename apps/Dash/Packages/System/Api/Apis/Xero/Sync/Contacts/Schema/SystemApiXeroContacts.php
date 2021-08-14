<?php

namespace Apps\Dash\Packages\System\Api\Apis\Xero\Sync\Contacts\Schema;

use Phalcon\Db\Column;

class SystemApiXeroContacts
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
                    'baz_vendor_id',
                    [
                        'type'          => Column::TYPE_INTEGER,
                        'notNull'       => false
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
                    'ContactID',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 50,
                        'notNull'       => false
                    ]
                ),
                new Column(
                    'AccountNumber',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 50,
                        'notNull'       => false
                    ]
                ),
                new Column(
                    'ContactStatus',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 20,
                        'notNull'       => false
                    ]
                ),
                new Column(
                    'Name',//Business_name
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 100,
                        'notNull'       => false,
                    ]
                ),
                new Column(
                    'FirstName',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 50,
                        'notNull'       => false,
                    ]
                ),
                new Column(
                    'LastName',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 50,
                        'notNull'       => false,
                    ]
                ),
                new Column(
                    'EmailAddress',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 100,
                        'notNull'       => false,
                    ]
                ),
                new Column(
                    'ContactGroups',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 1024,
                        'notNull'       => false
                    ]
                ),
                new Column(
                    'IsSupplier',
                    [
                        'type'          => Column::TYPE_TINYINTEGER,
                        'notNull'       => false
                    ]
                ),
                new Column(
                    'IsCustomer',
                    [
                        'type'          => Column::TYPE_TINYINTEGER,
                        'notNull'       => false
                    ]
                ),
                new Column(
                    'HasAttachments',
                    [
                        'type'          => Column::TYPE_BOOLEAN,
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
                    'UpdatedDateUTC',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 50,
                        'notNull'       => false
                    ]
                ),
                new Column(
                    'BrandingTheme',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 1024,
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