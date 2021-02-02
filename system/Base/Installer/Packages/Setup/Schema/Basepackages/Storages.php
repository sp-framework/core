<?php

namespace System\Base\Installer\Packages\Setup\Schema\Basepackages;

use Phalcon\Db\Column;

class Storages
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
                        'name',
                        [
                            'type'    => Column::TYPE_VARCHAR,
                            'size'    => 50,
                            'notNull' => true,
                        ]
                    ),
                    new Column(
                        'type',
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
                        'permission',
                        [
                            'type'    => Column::TYPE_VARCHAR,
                            'size'    => 50,
                            'notNull' => true,
                        ]
                    ),
                    new Column(
                        'allowed_image_mime_types',
                        [
                            'type'      => Column::TYPE_VARCHAR,
                            'size'      => 2048,
                            'notNull'   => 'true'
                        ]
                    ),
                    new Column(
                        'allowed_image_sizes',
                        [
                            'type'      => Column::TYPE_VARCHAR,
                            'size'      => 1024,
                            'notNull'   => 'true'
                        ]
                    ),
                    new Column(
                        'images_path',
                        [
                            'type'      => Column::TYPE_VARCHAR,
                            'size'      => 1024,
                            'notNull'   => 'true'
                        ]
                    ),
                    new Column(
                        'cache_path',
                        [
                            'type'      => Column::TYPE_VARCHAR,
                            'size'      => 1024,
                            'notNull'   => 'true'
                        ]
                    ),
                    new Column(
                        'max_image_size',
                        [
                            'type'      => Column::TYPE_SMALLINTEGER,
                            'notNull'   => 'true'
                        ]
                    ),
                    new Column(
                        'default_image_quality',
                        [
                            'type'      => Column::TYPE_TINYINTEGER,
                            'notNull'   => 'true'
                        ]
                    ),
                    new Column(
                        'max_image_file_size',
                        [
                            'type'      => Column::TYPE_INTEGER,
                            'notNull'   => 'true'
                        ]
                    ),
                    new Column(
                        'allowed_file_mime_types',
                        [
                            'type'      => Column::TYPE_VARCHAR,
                            'size'      => 2048,
                            'notNull'   => 'true'
                        ]
                    ),
                    new Column(
                        'data_path',
                        [
                            'type'      => Column::TYPE_VARCHAR,
                            'size'      => 1024,
                            'notNull'   => 'true'
                        ]
                    ),
                    new Column(
                        'max_data_file_size',
                        [
                            'type'      => Column::TYPE_INTEGER,
                            'notNull'   => 'true'
                        ]
                    )
                ]
            ];
    }
}