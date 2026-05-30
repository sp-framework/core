<?php

namespace System\Base\Installer\Packages\Setup\Schema\Basepackages\Email;

use Phalcon\Db\Column;
use Phalcon\Db\Index;

class Queue
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
                        'app_id',
                        [
                            'type'    => Column::TYPE_INTEGER,
                            'notNull' => true,
                        ]
                    ),
                    new Column(
                        'domain_id',
                        [
                            'type'    => Column::TYPE_INTEGER,
                            'notNull' => true,
                        ]
                    ),
                    new Column(
                        'status',
                        [
                            'type'    => Column::TYPE_TINYINTEGER,
                            'notNull' => true,
                        ]
                    ),
                    new Column(
                        'priority',
                        [
                            'type'    => Column::TYPE_TINYINTEGER,
                            'notNull' => true,
                        ]
                    ),
                    new Column(
                        'sent_on',
                        [
                            'type'    => Column::TYPE_VARCHAR,
                            'size'    => 50,
                            'notNull' => false,
                        ]
                    ),
                    new Column(
                        'confidential',
                        [
                            'type'    => Column::TYPE_TINYINTEGER,
                            'notNull' => false,
                        ]
                    ),
                    new Column(
                        'to_addresses',
                        [
                            'type'    => Column::TYPE_JSON,
                            'notNull' => true,
                        ]
                    ),
                    new Column(
                        'from_address',
                        [
                            'type'    => Column::TYPE_VARCHAR,
                            'size'    => 100,
                            'notNull' => true,
                        ]
                    ),
                    new Column(
                        'from_name',
                        [
                            'type'    => Column::TYPE_VARCHAR,
                            'size'    => 100,
                            'notNull' => true,
                        ]
                    ),
                    new Column(
                        'cc_addresses',
                        [
                            'type'    => Column::TYPE_JSON,
                            'notNull' => false,
                        ]
                    ),
                    new Column(
                        'bcc_addresses',
                        [
                            'type'    => Column::TYPE_JSON,
                            'notNull' => false,
                        ]
                    ),
                    new Column(
                        'attachments',
                        [
                            'type'    => Column::TYPE_JSON,
                            'notNull' => false,
                        ]
                    ),
                    new Column(
                        'subject',
                        [
                            'type'    => Column::TYPE_VARCHAR,
                            'size'    => 1024,
                            'notNull' => true,
                        ]
                    ),
                    new Column(
                        'body',
                        [
                            'type'    => Column::TYPE_MEDIUMTEXT,
                            'notNull' => true
                        ]
                    ),
                    new Column(
                        'logs',
                        [
                            'type'    => Column::TYPE_TEXT,
                            'notNull' => false
                        ]
                    )
                ]
            ];
    }

    public function indexes()
    {
        return
        [
            new Index(
                'column_INDEX',
                [
                    'status',
                    'priority',
                    'from_address'
                ],
                'INDEX'
            )
        ];
    }
}