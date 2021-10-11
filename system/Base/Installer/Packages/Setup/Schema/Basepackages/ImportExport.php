<?php

namespace System\Base\Installer\Packages\Setup\Schema\Basepackages;

use Phalcon\Db\Column;

class ImportExport
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
                        'type',
                        [
                            'type'          => Column::TYPE_VARCHAR,
                            'size'          => 50,
                            'notNull'       => true,
                        ]
                    ),
                    new Column(
                        'status',
                        [
                            'type'          => Column::TYPE_TINYINTEGER,
                            'notNull'       => true,
                        ]
                    ),
                    new Column(
                        'component_id',
                        [
                            'type'          => Column::TYPE_SMALLINTEGER,
                            'notNull'       => true,
                        ]
                    ),
                    new Column(
                        'filter_id',
                        [
                            'type'          => Column::TYPE_SMALLINTEGER,
                            'notNull'       => false,
                        ]
                    ),
                    new Column(
                        'fields',
                        [
                            'type'          => Column::TYPE_TEXT,
                            'notNull'       => false,
                        ]
                    ),
                    new Column(
                        'app_id',
                        [
                            'type'          => Column::TYPE_TINYINTEGER,
                            'notNull'       => true,
                        ]
                    ),
                    new Column(
                        'account_id',
                        [
                            'type'          => Column::TYPE_INTEGER,
                            'notNull'       => true,
                        ]
                    ),
                    new Column(
                        'email_to',
                        [
                            'type'          => Column::TYPE_VARCHAR,
                            'size'          => 2048,
                            'notNull'       => true,
                        ]
                    ),
                    new Column(
                        'job_id',
                        [
                            'type'          => Column::TYPE_INTEGER,
                            'notNull'       => false,
                        ]
                    ),
                    new Column(
                        'file',
                        [
                            'type'          => Column::TYPE_VARCHAR,
                            'size'          => 1024,
                            'notNull'       => false,
                        ]
                    ),
                    new Column(
                        'logs',
                        [
                            'type'          => Column::TYPE_MEDIUMTEXT,
                            'notNull'       => false,
                        ]
                    )
                ]
            ];
    }
}