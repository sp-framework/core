<?php

namespace System\Base\Installer\Packages\Setup\Schema\Basepackages;

use Phalcon\Db\Column;

class Notes
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
                        'note_type',
                        [
                            'type'    => Column::TYPE_TINYINTEGER,
                            'notNull' => true
                        ]
                    ),
                    new Column(
                        'note_app_visibility',
                        [
                            'type'    => Column::TYPE_VARCHAR,
                            'size'    => 100,
                            'notNull' => false
                        ]
                    ),
                    new Column(
                        'account_id',
                        [
                            'type'    => Column::TYPE_INTEGER,
                            'notNull' => true
                        ]
                    ),
                    new Column(
                        'is_private',
                        [
                            'type'    => Column::TYPE_TINYINTEGER,
                            'notNull' => false
                        ]
                    ),
                    new Column(
                        'created_at',
                        [
                            'type'    => Column::TYPE_TIMESTAMP,
                            'notNull' => true,
                            'default' => 'CURRENT_TIMESTAMP'
                        ]
                    ),
                    new Column(
                        'package_name',
                        [
                            'type'    => Column::TYPE_VARCHAR,
                            'size'    => 100,
                            'notNull' => true
                        ]
                    ),
                    new Column(//Source Row Id
                        'package_row_id',
                        [
                            'type'    => Column::TYPE_INTEGER,
                            'notNull' => true
                        ]
                    ),
                    new Column(
                        'note',
                        [
                            'type'    => Column::TYPE_MEDIUMTEXT,
                            'notNull' => true,
                        ]
                    ),
                    new Column(
                        'note_attachments',
                        [
                            'type'    => Column::TYPE_VARCHAR,
                            'size'    => 1024,
                            'notNull' => false
                        ]
                    )
                ]
            ];
    }
}