<?php

namespace Applications\Ecom\Admin\Packages\Locations\Settings\Types\Install\Schema;

use Phalcon\Db\Column;

class LocationsTypes
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
                    'description',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 4096,
                        'notNull' => false,
                    ]
                )
            ]
        ];
    }
}