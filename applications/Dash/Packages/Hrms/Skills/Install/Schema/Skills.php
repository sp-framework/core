<?php

namespace Applications\Dash\Packages\Hrms\Skills\Install\Schema;

use Phalcon\Db\Column;

class Skills
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
                        'size'    => 100,
                        'notNull' => true,
                    ]
                ),
                new Column(
                    'skill_user_ids',
                    [
                        'type'    => Column::TYPE_VARCHAR,
                        'size'    => 2048,
                        'notNull' => false,
                    ]
                ),
            ]
        ];
    }
}