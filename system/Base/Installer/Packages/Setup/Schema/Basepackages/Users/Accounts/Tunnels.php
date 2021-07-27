<?php

namespace System\Base\Installer\Packages\Setup\Schema\Basepackages\Users\Accounts;

use Phalcon\Db\Column;

class Tunnels
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
                        'account_id',
                        [
                            'type'          => Column::TYPE_INTEGER,
                            'notNull'       => true,
                        ]
                    ),
                    new Column(
                        'notifications_tunnel',
                        [
                            'type'          => Column::TYPE_SMALLINTEGER,
                            'notNull'       => false,
                        ]
                    ),
                    new Column(
                        'messenger_tunnel',
                        [
                            'type'          => Column::TYPE_SMALLINTEGER,
                            'notNull'       => false,
                        ]
                    )
                ]
            ];
    }
}