<?php

namespace System\Base\Installer\Packages\Setup\Schema;

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
                            'type'          => Column::TYPE_TINYINTEGER,
                            'notNull'       => true,
                            'autoIncrement' => true,
                            'primary'       => true,
                        ]
                    ),
                    new Column(
                        'application_id',
                        [
                            'type'    => Column::TYPE_TINYINTEGER,
                            'notNull' => true,
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