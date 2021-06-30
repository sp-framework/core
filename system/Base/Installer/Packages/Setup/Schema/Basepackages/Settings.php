<?php

namespace System\Base\Installer\Packages\Setup\Schema\Basepackages;

use Phalcon\Db\Column;

class Settings
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
                        'package_name',
                        [
                            'type'    => Column::TYPE_VARCHAR,
                            'size'    => 100,
                            'notNull' => true
                        ]
                    ),
                    new Column(
                        'settings',
                        [
                            'type'    => Column::TYPE_TEXT,
                            'notNull' => true,
                        ]
                    )
                ]
            ];
    }
}