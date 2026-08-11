<?php

namespace System\Base\Installer\Packages\Setup\Schema\Providers\Access;

use Phalcon\Db\Column;
use Phalcon\Db\Index;

class IpFiltersIp2locationStates
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
                    'name',
                    [
                        'type'          => Column::TYPE_VARCHAR,
                        'size'          => 255,
                        'notNull'       => true,
                    ]
                ),
                new Column(
                    'country_id',
                    [
                        'type'          => Column::TYPE_INTEGER,
                        'notNull'       => true,
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
                    'name',
                    'country_id'
                ],
                'INDEX'
            )
        ];
    }
}