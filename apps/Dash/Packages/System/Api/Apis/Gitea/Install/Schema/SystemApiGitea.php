<?php

namespace Apps\Dash\Packages\System\Api\Apis\Gitea\Install\Schema;

use Phalcon\Db\Column;

class SystemApiGitea
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
                )
            ],
            'options'   => [
                'TABLE_COLLATION' => 'utf8mb4_general_ci'
            ]
        ];
    }
}