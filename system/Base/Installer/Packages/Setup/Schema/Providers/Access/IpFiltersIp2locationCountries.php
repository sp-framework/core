<?php

namespace System\Base\Installer\Packages\Setup\Schema\Providers\Access;

use Phalcon\Db\Column;
use Phalcon\Db\Index;

class IpFiltersIp2locationCountries
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
                        'notNull'       => true
                    ]
                ),
                new Column(
                    'iso2',
                    [
                        'type'          => Column::TYPE_CHAR,
                        'size'          => 2,
                        'notNull'       => false
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
                'column_UNIQUE',
                [
                    'name',
                    'iso2'
                ],
                'UNIQUE'
            ),
            new Index(
                'column_INDEX',
                [
                    'name',
                    'iso2'
                ],
                'INDEX'
            )
        ];
    }
}