<?php

namespace System\Base\Installer\Packages\Setup\Schema\Basepackages\Storages;

use Phalcon\Db\Column;

class StoragesLocal
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
                        'storages_id',
                        [
                            'type'    => Column::TYPE_TINYINTEGER,
                            'notNull' => true,
                        ]
                    ),
                    new Column(
                        'uuid',
                        [
                            'type'    => Column::TYPE_VARCHAR,
                            'size'    => 1024,
                            'notNull' => true,
                        ]
                    ),
                    new Column(
                        'uuid_location',
                        [
                            'type'    => Column::TYPE_VARCHAR,
                            'size'    => 4096,
                            'notNull' => true,
                        ]
                    ),
                    new Column(
                        'links',
                        [
                            'type'    => Column::TYPE_VARCHAR,
                            'size'    => 4096,
                            'notNull' => false,
                        ]
                    ),
                    new Column(
                        'org_file_name',
                        [
                            'type'    => Column::TYPE_VARCHAR,
                            'size'    => 1024,
                            'notNull' => true,
                        ]
                    ),
                    new Column(
                        'size',
                        [
                            'type'      => Column::TYPE_INTEGER,
                            'notNull'   => true
                        ]
                    ),
                    new Column(
                        'type',
                        [
                            'type'    => Column::TYPE_VARCHAR,
                            'size'    => 1024,
                            'notNull' => true,
                        ]
                    ),
                    new Column(
                        'is_pointer',
                        [
                            'type'    => Column::TYPE_BOOLEAN,
                            'notNull' => true,
                        ]
                    ),
                    new Column(
                        'orphan',
                        [
                            'type'    => Column::TYPE_BOOLEAN,
                            'notNull' => true,
                        ]
                    ),
                    new Column(
                        'created_by',
                        [
                            'type'    => Column::TYPE_INTEGER,
                            'notNull' => false,
                        ]
                    ),
                    new Column(
                        'updated_by',
                        [
                            'type'    => Column::TYPE_INTEGER,
                            'notNull' => false,
                        ]
                    ),
                    new Column(
                        'created',
                        [
                            'type'    => Column::TYPE_TIMESTAMP,
                            'notNull' => true,
                            'default' => 'CURRENT_TIMESTAMP',
                        ]
                    ),
                    new Column(
                        'updated',
                        [
                            'type'    => Column::TYPE_TIMESTAMP,
                            'notNull' => true,
                            'default' => 'CURRENT_TIMESTAMP',
                        ]
                    )
                ]
            ];
    }
}