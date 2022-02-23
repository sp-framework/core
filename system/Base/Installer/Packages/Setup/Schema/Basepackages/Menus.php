<?php

namespace System\Base\Installer\Packages\Setup\Schema\Basepackages;

use Phalcon\Db\Column;

class Menus
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
                        'menu',
                        [
                            'type'    => Column::TYPE_VARCHAR,
                            'size'    => 4096,
                            'notNull' => true,
                        ]
                    ),
                    new Column(
                        'apps',
                        [
                            'type'    => Column::TYPE_VARCHAR,
                            'size'    => 2048,
                            'notNull' => true,
                        ]
                    ),
                    new Column(
                        'sequence',
                        [
                            'type'    => Column::TYPE_INTEGER,
                            'size'    => 3,
                            'notNull' => true,
                        ]
                    )
                ]
            ];
    }
}