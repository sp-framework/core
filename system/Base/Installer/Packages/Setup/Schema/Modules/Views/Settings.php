<?php

namespace System\Base\Installer\Packages\Setup\Schema\Modules\Views;

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
                            'type'          => Column::TYPE_SMALLINTEGER,
                            'notNull'       => true,
                            'autoIncrement' => true,
                            'primary'       => true,
                        ]
                    ),
                    new Column(
                        'view_id',
                        [
                            'type'    => Column::TYPE_SMALLINTEGER,
                            'notNull' => true,
                        ]
                    ),
                    new Column(
                        'domain_id',
                        [
                            'type'    => Column::TYPE_SMALLINTEGER,
                            'notNull' => true,
                        ]
                    ),
                    new Column(
                        'app_id',
                        [
                            'type'    => Column::TYPE_SMALLINTEGER,
                            'notNull' => true,
                        ]
                    ),
                    new Column(
                        'settings',
                        [
                            'type'    => Column::TYPE_JSON,
                            'notNull' => false,
                        ]
                    )
                ]
            ];
    }
}