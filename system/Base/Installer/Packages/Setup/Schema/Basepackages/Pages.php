<?php

namespace System\Base\Installer\Packages\Setup\Schema\Basepackages;

use Phalcon\Db\Column;
use Phalcon\Db\Index;

class Pages
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
                            'size'          => 100,
                            'notNull'       => true
                        ]
                    ),
                    new Column(
                        'description',
                        [
                            'type'          => Column::TYPE_VARCHAR,
                            'size'          => 1024,
                            'notNull'       => false
                        ]
                    ),
                    new Column(
                        'html_code',
                        [
                            'type'          => Column::TYPE_MEDIUMTEXT,
                            'notNull'       => true,
                        ]
                    ),
                    new Column(
                        'murl_ids',
                        [
                            'type'          => Column::TYPE_JSON,
                            'notNull'       => false
                        ]
                    ),
                ]
            ];
    }
}
