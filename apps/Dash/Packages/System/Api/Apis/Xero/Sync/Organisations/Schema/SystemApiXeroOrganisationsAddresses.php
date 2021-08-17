<?php

namespace Apps\Dash\Packages\System\Api\Apis\Xero\Sync\Organisations\Schema;

use Phalcon\Db\Column;

class SystemApiXeroOrganisationsAddresses
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
                    'OrganisationID',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 50,
                        'notNull' => false
                    ]
                ),
                new Column(
                    'AddressType',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 50,
                        'notNull' => false
                    ]
                ),
                new Column(
                    'AddressLine1',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 500,
                        'notNull' => false
                    ]
                ),
                new Column(
                    'AddressLine2',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 500,
                        'notNull' => false
                    ]
                ),
                new Column(
                    'City',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 255,
                        'notNull' => false
                    ]
                ),
                new Column(
                    'PostalCode',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 50,
                        'notNull' => false
                    ]
                ),
                new Column(
                    'Country',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 50,
                        'notNull' => false
                    ]
                ),
                new Column(
                    'AttentionTo',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 255,
                        'notNull' => false
                    ]
                ),
            ],
            'options' => [
                'TABLE_COLLATION' => 'utf8mb4_general_ci'
            ]
        ];
    }
}