<?php

namespace Apps\Core\Packages\Devtools\Modules\Install\Schema;

use Phalcon\Db\Column;

class DevtoolsModulesBundles
{
    public function columns()
    {
        return
            [
               'columns' => [
                    new Column(
                        'id',
                        [
                            'type'          => Column::TYPE_SMALLINTEGER,
                            'notNull'       => true,
                            'autoIncrement' => true,
                            'primary'       => true,
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
                        'description',
                        [
                            'type'    => Column::TYPE_VARCHAR,
                            'size'    => 2048,
                            'notNull' => false,
                        ]
                    ),
                    new Column(
                        'module_type',
                        [
                            'type'    => Column::TYPE_VARCHAR,
                            'size'    => 50,
                            'notNull' => true,
                        ]
                    ),
                    new Column(
                        'app_type',
                        [
                            'type'    => Column::TYPE_VARCHAR,
                            'size'    => 50,
                            'notNull' => true,
                        ]
                    ),
                    new Column(
                        'repo',
                        [
                            'type'    => Column::TYPE_VARCHAR,
                            'size'    => 2048,
                            'notNull' => true,
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
                        'bundle_modules',
                        [
                            'type'    => Column::TYPE_JSON,
                            'notNull' => true,
                        ]
                    )
                ],
                'options' => [
                    'TABLE_COLLATION' => 'utf8mb4_general_ci'
                ]
            ];
    }
}