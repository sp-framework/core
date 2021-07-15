<?php

namespace Apps\Dash\Packages\System\Api\Install\Schema;

use Phalcon\Db\Column;
use Phalcon\Db\Reference;
use System\Base\BasePackage;

class SystemApi extends BasePackage
{
    public function columns()
    {
        return
        [
           'columns' => [
                new Column(
                    'id',
                    [
                        'type'          => Column::TYPE_TINYINTEGER,
                        'notNull'       => true,
                        'autoIncrement' => true,
                        'primary'       => true,
                    ]
                ),
                new Column(
                    'api_id',
                    [
                        'type'    => Column::TYPE_TINYINTEGER,
                        'notNull' => true,
                    ]
                ),
                new Column(
                    'name',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 50,
                        'notNull' => true,
                    ]
                ),
                new Column(
                    'api_type',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 50,
                        'notNull' => true,
                    ]
                ),
                new Column(
                    'in_use',
                    [
                        'type'    => Column::TYPE_TINYINTEGER,
                        'notNull' => false,
                    ]
                ),
                new Column(
                    'used_by',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 100,
                        'notNull' => false,
                    ]
                ),
                new Column(
                    'setup',
                    [
                        'type'    => Column::TYPE_TINYINTEGER,
                        'notNull' => true
                    ]
                ),
                new Column(
                    'description',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 2048,
                        'notNull' => false,
                    ]
                )
            ],
            'options' => [
                'TABLE_COLLATION' => 'utf8mb4_general_ci'
            ],
            // 'references' => [
            //     new Reference(
            //         'field_ebay_fk',
            //         [
            //             'referencedSchema'      => $this->config->db->dbname,
            //             'referencedTable'       => 'system_api_ebay',
            //             'columns'               => ['api_id'],
            //             'referencedColumns'     => ['id']
            //         ]
            //     ),
            //     new Reference(
            //         'field_generic_fk',
            //         [
            //             'referencedSchema'      => $this->config->db->dbname,
            //             'referencedTable'       => 'system_api_generic',
            //             'columns'               => ['api_id'],
            //             'referencedColumns'     => ['id']
            //         ]
            //     )
            // ]
        ];
    }
}