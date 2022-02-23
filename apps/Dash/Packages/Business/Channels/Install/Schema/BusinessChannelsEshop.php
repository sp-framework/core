<?php

namespace Apps\Dash\Packages\Business\Channels\Install\Schema;

use Phalcon\Db\Column;

class BusinessChannelsEshop
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
                    'domain_id',
                    [
                        'type'    => Column::TYPE_INTEGER,
                        'notNull' => true,
                    ]
                ),
                new Column(
                    'app_id',
                    [
                        'type'    => Column::TYPE_INTEGER,
                        'notNull' => true,
                    ]
                ),
            ],
            'options' => [
                'TABLE_COLLATION' => 'utf8mb4_general_ci'
            ]
        ];
    }
}