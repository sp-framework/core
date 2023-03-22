<?php

namespace System\Base\Installer\Packages\Setup\Schema\Basepackages\Dashboards;

use Phalcon\Db\Column;

class Widgets
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
                    'widget_id',
                    [
                        'type'          => Column::TYPE_INTEGER,
                        'notNull'       => true
                    ]
                ),
                new Column(
                    'dashboard_id',
                    [
                        'type'          => Column::TYPE_SMALLINTEGER,
                        'notNull'       => true
                    ]
                ),
                new Column(
                    'sequence',
                    [
                        'type'    => Column::TYPE_TINYINTEGER,
                        'notNull' => true
                    ]
                ),
                new Column(
                    'settings',
                    [
                        'type'    => Column::TYPE_JSON,
                        'notNull' => true
                    ]
                ),
            ]
        ];
    }
}