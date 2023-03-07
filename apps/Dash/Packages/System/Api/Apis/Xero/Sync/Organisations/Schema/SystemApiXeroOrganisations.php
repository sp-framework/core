<?php

namespace Apps\Dash\Packages\System\Api\Apis\Xero\Sync\Organisations\Schema;

use Phalcon\Db\Column;

class SystemApiXeroOrganisations
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
                    'baz_entity_id',
                    [
                        'type'          => Column::TYPE_TINYINTEGER,
                        'notNull'       => false,
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
                    'OrganisationID',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 50,
                        'notNull'       => true
                    ]
                ),
                new Column(
                    'APIKey',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 100,
                        'notNull'       => false
                    ]
                ),
                new Column(
                    'Name',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 100,
                        'notNull'       => true,
                    ]
                ),
                new Column(
                    'LegalName',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 100,
                        'notNull'       => true,
                    ]
                ),
                new Column(
                    'PaysTax',
                    [
                        'type'          => Column::TYPE_TINYINTEGER,
                        'notNull'       => false,
                    ]
                ),
                new Column(
                    'Version',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 10,
                        'notNull'       => false
                    ]
                ),
                new Column(
                    'OrganisationType',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 20,
                        'notNull'       => false
                    ]
                ),
                new Column(
                    'CountryCode',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 3,
                        'notNull'       => false
                    ]
                ),
                new Column(
                    'IsDemoCompany',
                    [
                        'type'          => Column::TYPE_TINYINTEGER,
                        'notNull'       => false,
                    ]
                ),
                new Column(
                    'OrganisationStatus',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 20,
                        'notNull'       => false
                    ]
                ),
                new Column(
                    'RegistrationNumber',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 50,
                        'notNull'       => false
                    ]
                ),
                new Column(
                    'PeriodLockDate',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 50,
                        'notNull'       => false
                    ]
                ),
                new Column(
                    'CreatedDateUTC',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 50,
                        'notNull'       => false
                    ]
                ),
                new Column(
                    'Timezone',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 50,
                        'notNull'       => false
                    ]
                ),
                new Column(
                    'OrganisationEntityType',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 50,
                        'notNull'       => false
                    ]
                ),
                new Column(
                    'ShortCode',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 50,
                        'notNull'       => false
                    ]
                ),
                new Column(
                    'Class',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 50,
                        'notNull'       => false
                    ]
                ),
                new Column(
                    'Edition',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 50,
                        'notNull'       => false
                    ]
                ),
                new Column(
                    'ExternalLinks',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 2048,
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