<?php

namespace Apps\Core\Packages\Devtools\Modules\Install\Schema;

use Phalcon\Db\Column;
use Phalcon\Db\Index;

class FilesHash
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
                    'module_type',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 50,
                        'notNull'       => true,
                    ]
                ),
                new Column(
                    'module_id',
                    [
                        'type'          => Column::TYPE_INTEGER,
                        'notNull'       => true,
                    ]
                ),
                new Column(
                    'files_hash',
                    [
                        'type'          => Column::TYPE_JSON,
                        'notNull'       => true,
                    ]
                ),
                new Column(
                    'release_pending',
                    [
                        'type'          => Column::TYPE_BOOLEAN,
                        'notNull'       => false,
                    ]
                )
            ],
            'indexes' => [
                new Index(
                    'column_UNIQUE',
                    [
                        'module_type',
                        'module_id'
                    ],
                    'UNIQUE'
                )
            ],
            'options' => [
                'TABLE_COLLATION' => 'utf8mb4_general_ci'
            ]
        ];
    }
}
